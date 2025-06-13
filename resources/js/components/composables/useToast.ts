import { ref } from 'vue';

export type ToastType = 'default' | 'success' | 'error';

interface Toast {
    id: number;
    message: string;
    type: ToastType;
}

export const toasts = ref<Toast[]>([]);
let toastId = 0;

export function useToast() {
    const addToast = (message: string, type: ToastType = 'default') => {
        const id = ++toastId;
        toasts.value.push({ id, message, type });

        // Auto-remove after 3s
        setTimeout(() => {
            toasts.value = toasts.value.filter((t) => t.id !== id);
        }, 3000);
    };

    return {
        addToast,
    };
}
