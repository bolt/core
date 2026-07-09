import Axios from 'axios';
import { Modal } from 'bootstrap';
import { renable } from '../../patience-is-a-virtue';
import { resetModalContent } from '../../modal';

export type ServerFile = {
    group?: string;
    value: string;
    text: string;
    base_url_path?: string;
};

type BrowserOptions = {
    initialFilelist?: string;
    extensions: string[];
    labels: Record<string, string>;
    modalTitlePrefix: string;
    generateModalContent: (inputOptions: ServerFile[]) => string;
    onSelect: (selectedFile: string) => void;
    onOpenError: (error: BrowserError) => void;
    onNavigateError: (error: BrowserError) => void;
};

type BrowserErrorResponse = {
    data?: string | { error?: { message?: string } };
};

type BrowserErrorInput =
    | Error
    | {
          message?: string;
          response?: BrowserErrorResponse;
      };

class BrowserError extends Error {
    response?: BrowserErrorResponse;

    constructor(message: string, response?: BrowserErrorResponse) {
        super(message);
        this.name = 'BrowserError';
        this.response = response;
    }
}

export function createServerFileBrowser(options: BrowserOptions) {
    let currentFilelist = options.initialFilelist;

    function filterServerFiles(files: ServerFile[]) {
        return files.filter(file => {
            if (file.group === 'directories') {
                return true;
            }

            const ext = /(?:\.([^.]+))?$/.exec(file.text)?.[1];
            return ext ? options.extensions.includes(ext) : false;
        });
    }

    function selectServerFile(event: Event) {
        return loadModal(currentFilelist, 'open', event);
    }

    function navigateDirectory() {
        return loadModal(currentFilelist, 'navigate');
    }

    function loadModal(filelist: string | undefined, mode: 'open' | 'navigate', event?: Event) {
        if (!filelist) {
            return Promise.reject(new Error('Missing server file list URL'));
        }

        return Axios.get<ServerFile[]>(filelist)
            .then(res => {
                const inputOptions = filterServerFiles(res.data);
                if (mode === 'open') {
                    if (event) {
                        openModal(event, inputOptions);
                    }
                } else {
                    refreshModal(inputOptions);
                }
            })
            .catch(err => {
                const error = normalizeError(err as BrowserErrorInput);
                if (mode === 'open') {
                    options.onOpenError(error);
                } else {
                    options.onNavigateError(error);
                }
                renable();
            });
    }

    function normalizeError(error: BrowserErrorInput): BrowserError {
        if (error instanceof BrowserError) {
            return error;
        }

        // Axios errors are `Error` instances that also carry a `.response`; keep it.
        const response = (error as { response?: BrowserErrorResponse }).response;
        const responseData = response?.data;

        // Prefer the server-provided message (a string body or a
        // `{ error: { message } }` payload) over Axios's generic "Request failed
        // with status code N". This must run BEFORE any `error.message` fallback,
        // because AxiosErrors are `Error` instances and would otherwise short-circuit
        // to the generic message, making the server message unreachable.
        const messageFromResponse = typeof responseData === 'string' ? responseData : responseData?.error?.message;

        return new BrowserError(messageFromResponse ?? error.message ?? String(error), response);
    }

    function requireElement<T extends Element>(selector: string, root: Document | Element = document): T {
        const element = root.querySelector<T>(selector);
        if (!element) {
            throw new Error(`Missing required element: ${selector}`);
        }
        return element;
    }

    type ModalParts = {
        resourcesModal: HTMLElement;
        resourcesModalObject: Modal;
        modalDialog: HTMLElement;
        modalTitle: HTMLElement;
        modalBody: HTMLElement;
        modalFooter: Element | null;
    };

    function getModalParts(): ModalParts {
        const resourcesModal = requireElement<HTMLElement>('#resourcesModal');
        const resourcesModalObject = Modal.getOrCreateInstance(resourcesModal);

        return {
            resourcesModal,
            resourcesModalObject,
            modalDialog: requireElement<HTMLElement>('.modal-dialog', resourcesModal),
            modalTitle: requireElement<HTMLElement>('.modal-title', resourcesModal),
            modalBody: requireElement<HTMLElement>('.modal-body', resourcesModal),
            modalFooter: resourcesModal.querySelector('.modal-footer'),
        };
    }

    function openModal(event: Event, inputOptions: ServerFile[]) {
        const parts = getModalParts();
        const { resourcesModal, modalDialog, modalTitle, modalBody, modalFooter } = parts;
        const button = (event.currentTarget || event.target) as HTMLElement;
        const dialogClass = button.getAttribute('data-modal-dialog-class');

        if (dialogClass) {
            modalDialog.classList.add(dialogClass);
        }

        modalTitle.innerHTML = button.getAttribute('data-modal-title') || '';
        modalBody.innerHTML = options.generateModalContent(inputOptions);
        modalFooter?.remove();

        bindModalInteractions(parts);

        resourcesModal.addEventListener(
            'hidden.bs.modal',
            () => {
                const selectedInput = modalBody.querySelector(
                    'input[type=checkbox]:checked',
                ) as HTMLInputElement | null;

                if (selectedInput) {
                    options.onSelect(selectedInput.value.replace('files/', ''));
                }

                resetModalContent(options.labels);
            },
            { once: true },
        );

        renable();
    }

    function refreshModal(inputOptions: ServerFile[]) {
        if (inputOptions.length === 0) {
            return;
        }

        const parts = getModalParts();
        const { modalTitle, modalBody } = parts;
        const folderPathChunks = inputOptions[0].value.split('/');
        folderPathChunks.pop();
        const folderPath = folderPathChunks.join('/');

        modalTitle.innerHTML = `${options.modalTitlePrefix}: <i class="fas fa-solid fa-folder-tree"></i>${folderPath}`;
        modalBody.innerHTML = options.generateModalContent(inputOptions);

        bindModalInteractions(parts);
    }

    function bindModalInteractions({ resourcesModal, resourcesModalObject, modalBody }: ModalParts) {
        resourcesModal.querySelectorAll('.directory').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                currentFilelist = (link as HTMLAnchorElement).href;
                navigateDirectory();
            });
        });

        modalBody.querySelectorAll('.form-check-input').forEach(card => {
            card.addEventListener('click', () => {
                resourcesModalObject.hide();
            });
        });
    }

    return {
        filterServerFiles,
        selectServerFile,
        navigateDirectory,
    };
}
