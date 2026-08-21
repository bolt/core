import { afterEach, describe, expect, it, vi } from 'vitest';
import axios from 'axios';
import { getRecords } from '../../../assets/js/services/api/content';
import { fetchNews, getNews } from '../../../assets/js/services/api/dashboardNews';

vi.mock('axios', () => ({ default: { get: vi.fn() } }));

afterEach(() => {
    vi.mocked(axios.get).mockReset();
});

describe('getRecords', () => {
    it('reads and parses the cached records for a content type', () => {
        localStorage.setItem('records-pages', JSON.stringify([{ id: 1 }]));

        expect(getRecords('pages')).toEqual([{ id: 1 }]);
    });

    it('returns null when nothing is cached', () => {
        // getItem returns null, String() makes it "null", JSON.parse gives null back.
        expect(getRecords('pages')).toBeNull();
    });

    it('scopes the cache key to the content type', () => {
        localStorage.setItem('records-pages', JSON.stringify(['pages']));

        expect(getRecords('entries')).toBeNull();
    });

    it('throws when the cached value is not valid JSON', () => {
        localStorage.setItem('records-pages', 'not json');

        expect(() => getRecords('pages')).toThrow(SyntaxError);
    });
});

describe('getNews', () => {
    it('reads and parses the cached dashboard news', () => {
        localStorage.setItem('dashboardnews', JSON.stringify({ title: 'Bolt 6' }));

        expect(getNews()).toEqual({ title: 'Bolt 6' });
    });

    it('returns null when nothing is cached', () => {
        expect(getNews()).toBeNull();
    });

    it('throws when the cached value is not valid JSON', () => {
        localStorage.setItem('dashboardnews', '{oops');

        expect(() => getNews()).toThrow(SyntaxError);
    });
});

describe('fetchNews', () => {
    it('requests the news endpoint and returns the payload', async () => {
        vi.mocked(axios.get).mockResolvedValue({ data: { title: 'Bolt 6' } });

        await expect(fetchNews()).resolves.toEqual({ title: 'Bolt 6' });
        expect(axios.get).toHaveBeenCalledWith('/async/news');
    });

    it('caches a text payload in localStorage', async () => {
        vi.mocked(axios.get).mockResolvedValue({ data: 'raw payload' });

        await fetchNews();

        expect(localStorage.getItem('dashboardnews')).toBe('raw payload');
    });

    it('caches a parsed payload unserialised, so getNews cannot read it back', async () => {
        // Pinning current behaviour, which is a bug. axios parses a JSON
        // response into an object, and fetchNews hands that straight to
        // localStorage.setItem, which stores "[object Object]". getNews then
        // fails to parse it. Nothing calls either function today.
        vi.mocked(axios.get).mockResolvedValue({ data: { title: 'Bolt 6' } });

        await fetchNews();

        expect(localStorage.getItem('dashboardnews')).toBe('[object Object]');
        expect(() => getNews()).toThrow(SyntaxError);
    });

    it('propagates a request failure', async () => {
        vi.mocked(axios.get).mockRejectedValue(new Error('offline'));

        await expect(fetchNews()).rejects.toThrow('offline');
        expect(localStorage.getItem('dashboardnews')).toBeNull();
    });
});
