/// <reference types="svelte" />

interface Word {
    id: string;
    word: string;
    original: string;
    meaning: string;
    extra1: string|null;
    extra2: string|null;
    extra3: string|null;
    type: string;
}
