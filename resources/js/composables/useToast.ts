import { toast } from 'vue-sonner'

export function useToast() {
  return {
    success: (message: string, options?: any) => toast.success(message, options),
    error: (message: string, options?: any) => toast.error(message, options),
    info: (message: string, options?: any) => toast(message, { ...options, style: 'info' }),
    warning: (message: string, options?: any) => toast(message, { ...options, style: 'warning' }),
    promise: toast.promise,
    dismiss: toast.dismiss,
  }
}
