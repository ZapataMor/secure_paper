export const DRAFT_TTL = 24 * 60 * 60 * 1000;
const uuid = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;

// sessionStorage pertenece a esta pestaña; cada cuenta utiliza una clave distinta.
export function uploadDraft({ userId, saved = false, scope = 'upload' }) {
    return {
        draftKey: `propaps:${scope}-draft:v1:${userId}`,
        pendingDraft: '',
        pendingSubmission: '',
        hasPendingDraft: false,
        draftSaved: false,
        draftWarning: '',
        initDraft() {
            this.notes = this.$refs.notes.value;
            try {
                if (saved) {
                    sessionStorage.removeItem(this.draftKey);
                    return;
                }
                const raw = sessionStorage.getItem(this.draftKey);
                if (raw) {
                    const draft = JSON.parse(raw);
                    if (typeof draft.text !== 'string' || draft.text.length > 8000
                        || !Number.isFinite(draft.updatedAt) || draft.updatedAt > Date.now()
                        || Date.now() - draft.updatedAt >= DRAFT_TTL) {
                        sessionStorage.removeItem(this.draftKey);
                    } else {
                        this.pendingDraft = draft.text;
                        this.pendingSubmission = typeof draft.submissionId === 'string' && uuid.test(draft.submissionId) ? draft.submissionId : '';
                        this.hasPendingDraft = Boolean(draft.text.trim() || draft.hadFiles === true);
                    }
                }
                // El borrador devuelto por el servidor tras una validación tiene prioridad.
                if (this.notes) this.saveDraft();
            } catch {
                this.draftWarning = 'No pudimos recuperar el borrador temporal. Conserva una copia de tus indicaciones antes de salir.';
            }
        },
        saveDraft() {
            this.pendingDraft = '';
            this.hasPendingDraft = false;
            try {
                if (this.notes.length > 8000) {
                    this.draftSaved = false;
                    this.draftWarning = 'Reduce las indicaciones a 8.000 caracteres y conserva una copia del texto completo.';
                    return;
                }
                const hadFiles = Boolean(this.files?.length);
                if (this.notes.trim() || hadFiles) {
                    sessionStorage.setItem(this.draftKey, JSON.stringify({
                        text: this.notes, updatedAt: Date.now(), hadFiles,
                        submissionId: this.$refs.submission?.value ?? '',
                    }));
                    this.draftSaved = true;
                } else {
                    sessionStorage.removeItem(this.draftKey);
                    this.draftSaved = false;
                }
                this.draftWarning = '';
            } catch {
                this.draftSaved = false;
                this.draftWarning = 'Este navegador no permite guardar el borrador temporal. Copia tus indicaciones antes de salir o volver a ingresar.';
            }
        },
        recoverDraft() {
            this.notes = this.pendingDraft;
            this.$refs.notes.value = this.notes;
            if (this.pendingSubmission && this.$refs.submission) this.$refs.submission.value = this.pendingSubmission;
            this.error = '';
            // Conserva también la referencia de los envíos solo de archivos hasta reintentarlos.
            this.pendingDraft = '';
            this.hasPendingDraft = false;
        },
        discardDraft() {
            try {
                sessionStorage.removeItem(this.draftKey);
                this.pendingDraft = '';
                this.pendingSubmission = '';
                this.hasPendingDraft = false;
                this.draftSaved = false;
                this.draftWarning = '';
            } catch {
                this.draftWarning = 'No pudimos descartar el borrador temporal. Cierra esta pestaña para dejar de utilizarlo.';
            }
        },
    };
}
