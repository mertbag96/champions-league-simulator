import type { PageProps as InertiaPageProps } from '@inertiajs/core'

declare module '@inertiajs/core' {
  interface PageProps extends InertiaPageProps {
    flash: {
      success?: string
      error?: string
    }
  }
}
