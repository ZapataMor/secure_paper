import { test, beforeEach } from 'node:test';
import assert from 'node:assert/strict';
import { uploadDraft, DRAFT_TTL } from '../../resources/js/upload-draft.js';

let values;
beforeEach(() => {
    values = new Map();
    Object.defineProperty(globalThis, 'sessionStorage', { configurable: true, value: {
        getItem: key => values.get(key) ?? null,
        setItem: (key, value) => values.set(key, value),
        removeItem: key => values.delete(key),
    }});
});
function form(userId, options = {}) {
    const state = { ...uploadDraft({ userId, saved: options.saved, scope: options.scope }), notes: '', error: '',
        files: [], $refs: { notes: { value: options.oldInput ?? '' }, submission: { value: options.submissionId ?? crypto.randomUUID() } } };
    state.initDraft();
    return state;
}
test('expired session round trip offers the draft without posting or auto-filling', () => {
    const original = form(4);
    original.notes = 'Indicaciones ficticias aún no enviadas';
    original.saveDraft();
    const afterLogin = form(4);
    assert.equal(afterLogin.notes, '');
    assert.equal(afterLogin.pendingDraft, original.notes);
    afterLogin.recoverDraft();
    assert.equal(afterLogin.$refs.notes.value, original.notes);
    assert.equal(afterLogin.pendingDraft, '');
});
test('another account never receives the pending draft', () => {
    const a = form(4); a.notes = 'Borrador A'; a.saveDraft();
    const b = form(1); assert.equal(b.pendingDraft, '');
    b.notes = 'Borrador B'; b.saveDraft();
    assert.equal(form(4).pendingDraft, 'Borrador A');
    assert.equal(form(1).pendingDraft, 'Borrador B');
});
test('a confirmed send removes only that account draft', () => {
    for (const id of [1, 4]) { const f = form(id); f.notes = `Cuenta ${id}`; f.saveDraft(); }
    assert.equal(form(4, { saved: true }).pendingDraft, '');
    assert.equal(form(4).pendingDraft, '');
    assert.equal(form(1).pendingDraft, 'Cuenta 1');
});
test('validation errors keep the returned text instead of an older draft', () => {
    const f = form(4); f.notes = 'Versión antigua'; f.saveDraft();
    const afterError = form(4, { oldInput: 'Versión devuelta por el servidor' });
    assert.equal(afterError.notes, 'Versión devuelta por el servidor');
    assert.equal(afterError.pendingDraft, '');
    assert.equal(form(4).pendingDraft, afterError.notes);
});
test('expired or malformed records are never restored', () => {
    const key = form(4).draftKey;
    for (const draft of [
        { text: 'Caducado', updatedAt: Date.now() - DRAFT_TTL },
        { text: 'Fecha inválida', updatedAt: Date.now() + DRAFT_TTL },
        { text: 'a'.repeat(8001), updatedAt: Date.now() },
        { text: 7, updatedAt: Date.now() },
    ]) {
        values.set(key, JSON.stringify(draft));
        assert.equal(form(4).pendingDraft, '');
        assert.equal(values.has(key), false);
    }
    values.set(key, 'JSON incompleto');
    assert.equal(form(4).pendingDraft, '');
    assert.notEqual(form(4).draftWarning, '');
});
test('discarding and emptying remove the draft without submitting', () => {
    const f = form(4); f.notes = 'Texto'; f.saveDraft();
    form(4).discardDraft(); assert.equal(values.size, 0);
    f.saveDraft(); f.notes = ''; f.saveDraft(); assert.equal(values.size, 0);
});
test('blocked storage warns without erasing text or pretending it was saved', () => {
    sessionStorage.setItem = () => { throw new Error('Storage blocked'); };
    const f = form(4); f.notes = 'Texto que se puede copiar'; f.saveDraft();
    assert.equal(f.notes, 'Texto que se puede copiar');
    assert.equal(f.draftSaved, false);
    assert.match(f.draftWarning, /Copia tus indicaciones/);
});

test('a lost confirmation keeps the same submission identifier when recovering', () => {
    const original = form(4);
    const identifier = original.$refs.submission.value;
    original.notes = 'Texto enviado sin confirmación'; original.saveDraft();
    const retry = form(4);
    assert.notEqual(retry.$refs.submission.value, identifier);
    retry.recoverDraft();
    assert.equal(retry.$refs.submission.value, identifier);
    retry.saveDraft();
    const later = form(4); later.recoverDraft();
    assert.equal(later.$refs.submission.value, identifier);
});
test('file-only retries recover their identifier without retaining file contents', () => {
    const original = form(4);
    original.files = [{ name: 'ficticio.pdf', content: 'NO ALMACENAR' }];
    original.saveDraft();
    assert.equal([...values.values()].some(value => value.includes('NO ALMACENAR')), false);
    const retry = form(4);
    assert.equal(retry.hasPendingDraft, true);
    assert.equal(retry.notes, '');
    retry.recoverDraft();
    assert.equal(retry.$refs.submission.value, original.$refs.submission.value);
    assert.deepEqual(retry.files, []);
});
test('a server error with returned input preserves its supplied retry identifier', () => {
    const original = form(4); original.notes = 'Texto'; original.saveDraft();
    const retry = form(4, { oldInput: 'Texto', submissionId: original.$refs.submission.value });
    const reloaded = form(4); reloaded.recoverDraft();
    assert.equal(reloaded.$refs.submission.value, original.$refs.submission.value);
});

test('advisor drafts are isolated by account and target client', () => {
    const original = form(2, { scope: 'return:4' });
    original.notes = 'Devolución para cliente 4'; original.saveDraft();
    assert.equal(form(2, { scope: 'return:1' }).hasPendingDraft, false);
    assert.equal(form(3, { scope: 'return:4' }).hasPendingDraft, false);
    assert.equal(form(2).hasPendingDraft, false);
    const recovered = form(2, { scope: 'return:4' }); recovered.recoverDraft();
    assert.equal(recovered.notes, original.notes);
    assert.equal(recovered.$refs.submission.value, original.$refs.submission.value);
});

test('confirming a return clears only that client draft for that advisor', () => {
    for (const scope of ['return:4', 'return:1']) {
        const f = form(2, { scope }); f.notes = scope; f.saveDraft();
    }
    form(2, { scope: 'return:4', saved: true });
    assert.equal(form(2, { scope: 'return:4' }).hasPendingDraft, false);
    assert.equal(form(2, { scope: 'return:1' }).hasPendingDraft, true);
});