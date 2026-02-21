import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function slugify(value: string): string {
    return value
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\p{L}\p{N}-]/gu, '');
}
