import type { AxiosError } from 'axios';

export type UploadErrorResponse = string | { error?: { message?: string } };

/**
 * Extract a human-readable message from a failed file/image upload. The server
 * may return the message as a plain string or a `{ error: { message } }` object;
 * when neither is present we fall back to Axios's own error message (e.g. a
 * network failure or a non-JSON 5xx response), and finally to a generic string.
 * Shared by the File and Image editor components.
 */
export function getUploadErrorMessage(err: AxiosError<UploadErrorResponse>): string {
    const responseData = err.response?.data;
    if (typeof responseData === 'string') {
        return responseData;
    }

    return responseData?.error?.message ?? err.message ?? 'upload error';
}
