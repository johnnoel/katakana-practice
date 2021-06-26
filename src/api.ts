class Api {
    loadRandom(type: string): Promise<any> {
        return fetch('random.php?type=' + type, {
            method: 'GET',
            credentials: 'same-origin',
        });
    }
}

const api = new Api();

export default api;
