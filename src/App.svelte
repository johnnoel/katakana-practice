<script lang="ts">
    import api from './api';
    import Word from './Word.svelte';

    let showing = false;
    let word: Word | null = null;

    const handleClick = (type: string) => {
        api.loadRandom(type).then(resp => {
            if (resp.status !== 200) {
                throw 'doot'
            }

            return resp.json();
        }).then(json => {
            word = json as Word;
            showing = true;
        });
    };
</script>

<div>
    {#if word === null}
        <button type="button" on:click={() => handleClick('katakana')}>Katakana</button>
        <button type="button" on:click={() => handleClick('kanji')}>Kanji</button>
    {:else}
        <Word {...word} />
    {/if}
</div>

<style>
</style>
