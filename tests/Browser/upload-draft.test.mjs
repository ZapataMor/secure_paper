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
    const state = { ...uploadDraft({ userId, saved: options.saved }), notes: '', error: '',
        $refs: { notes: { value: options.oldInput ?? '' } } };
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
