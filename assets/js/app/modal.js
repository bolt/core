export function resetModalContent(labels) {
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
        labels['modal_button_deny'] +
        `" id="modalButtonDeny" type="button" class="btn btn-secondary" data-bs-dismiss="modal">` +
        labels['modal_button_deny'] +
        `</button>
                    <button data-modal-button-accept="` +
        labels['modal_button_save'] +
        `" id="modalButtonAccept" type="button" class="btn btn-primary" data-bs-dismiss="modal">` +
        labels['modal_button_save'] +
        `</button>
                </div>
            `;
    var resourcesModal = document.getElementById('resourcesModal');
    resourcesModal.querySelector('.modal-content').innerHTML = defaultContent;
}
