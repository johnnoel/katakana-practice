<script>
    import api from './api';

    let showingMeaning = false;

    const toggleMeaning = () => {
        showingMeaning = !showingMeaning;
    }

    const refresh = () => {
        api.loadRandom(type).then(resp => {
            if (resp.status !== 200) {
                throw 'doot';
            }

            return resp.json();
        }).then(json => {
            showingMeaning = false;
            word = json.word;
            id = json.id;
            meaning = json.meaning;
            original = json.original;
            extra1 = json.extra1;
            extra2 = json.extra2;
            extra3 = json.extra3;
        });
    };

    export let word;
    export let id;
    export let type;
    export let meaning;
    export let original;
    export let extra1;
    export let extra2;
    export let extra3;
</script>

<h1>{word}</h1>

<audio controls preload="none">
    <source type="audio/ogg" src="voice.php?id={id}">
</audio>

<button type="button" on:click={toggleMeaning}>Toggle meaning</button>

{#if showingMeaning}
    <div>
        <dl>
            <dt>Meaning:</dt>
            <dd>{meaning}</dd>

            <dt>Original line:</dt>
            <dd>{original}</dd>

            {#if extra1 !== null}
                <dt>Extra 1:</dt>
                <dd>{extra1}</dd>
            {/if}

            {#if extra2 !== null}
                <dt>Extra 2:</dt>
                <dd>{extra2}</dd>
            {/if}

            {#if extra3 !== null}
                <dt>Extra 3:</dt>
                <dd>{extra3}</dd>
            {/if}
        </dl>
    </div>
{/if}

<button type="button" on:click={refresh}>New word</button>
