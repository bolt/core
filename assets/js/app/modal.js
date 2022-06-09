export function resetModalContent(labels) {
    var resourcesModal = document.getElementById('resourcesModal');
    if (labels.modal_button_deny && labels.modal_button_save) {
        let deny_button = labels.modal_button_deny;
        let save_button = labels.modal_button_save;
        let defaultContent =
            `
                <div class="modal-header">
                    <h5 class="modal-title" id="resourcesModalLabel">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-modal-button-deny="` +
            deny_button +
            `" id="modalButtonDeny" type="button" class="btn btn-secondary" data-bs-dismiss="modal">` +
            deny_button +
            `</button>
                    <button data-modal-button-accept="` +
            save_button +
            `" id="modalButtonAccept" type="button" class="btn btn-primary" data-bs-dismiss="modal">` +
            save_button +
            `</button>
                </div>
            `;
        resourcesModal.querySelector('.modal-content').innerHTML = defaultContent;
    } else {
        let deny_button = 'Close';
        let save_button = 'Save';
        let defaultContent =
            `
                <div class="modal-header">
                    <h5 class="modal-title" id="resourcesModalLabel">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-modal-button-deny="` +
            deny_button +
            `" id="modalButtonDeny" type="button" class="btn btn-secondary" data-bs-dismiss="modal">` +
            deny_button +
            `</button>
                    <button data-modal-button-accept="` +
            save_button +
            `" id="modalButtonAccept" type="button" class="btn btn-primary" data-bs-dismiss="modal">` +
            save_button +
            `</button>
                </div>
            `;
        resourcesModal.querySelector('.modal-content').innerHTML = defaultContent;
    }
}
