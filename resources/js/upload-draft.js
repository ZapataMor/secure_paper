export const DRAFT_TTL = 24 * 60 * 60 * 1000;

// sessionStorage pertenece a esta pestaña; cada cuenta utiliza una clave distinta.
export function uploadDraft({ userId, saved = false }) {
    return {
        draftKey: `propaps:upload-draft:v1:${userId}`,
        pendingDraft: '',
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
            try {
                if (this.notes.length > 8000) {
                    this.draftSaved = false;
                    this.draftWarning = 'Reduce las indicaciones a 8.000 caracteres y conserva una copia del texto completo.';
                    return;
                }
                if (this.notes.trim()) {
                    sessionStorage.setItem(this.draftKey, JSON.stringify({ text: this.notes, updatedAt: Date.now() }));
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
            this.error = '';
            this.saveDraft();
        },
        discardDraft() {
            try {
                sessionStorage.removeItem(this.draftKey);
                this.pendingDraft = '';
                this.draftSaved = false;
                this.draftWarning = '';
            } catch {
                this.draftWarning = 'No pudimos descartar el borrador temporal. Cierra esta pestaña para dejar de utilizarlo.';
            }
        },
    };
}
