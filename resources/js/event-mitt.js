import mitt from "mitt";

export const FILE_UPLOAD_STARTED = 'FILE_UPLOAD_STARTED';
export const SHOW_ERROR_MESSAGE = 'SHOW_ERROR_MESSAGE';
export const SHOW_NOTIFICATION = 'SHOW_NOTIFICATION';
export const emitter = mitt();

export function showErrorMessage(message) {
    emitter.emit(SHOW_ERROR_MESSAGE, {message})
}

export function showSuccessNotification(message) {
    emitter.emit(SHOW_NOTIFICATION, {type: 'success', message})
}
export function showErrorNotification(message) {
    emitter.emit(SHOW_NOTIFICATION, {type: 'error', message})

}


