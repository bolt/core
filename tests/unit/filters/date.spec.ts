import { describe, expect, it } from 'vitest';
import { DateTime } from 'luxon';
import { date, datetime } from '../../../assets/js/filters/date';

// These render through Intl, so the exact output depends on the host locale.
// The assertions below pin the wiring and the shape rather than a literal
// string, so the suite stays stable wherever it runs.
describe('date', () => {
    it('renders an ISO date through luxon', () => {
        expect(date('2024-03-15')).toBe(DateTime.fromISO('2024-03-15').toLocaleString());
    });

    it('renders a date only, without a time of day', () => {
        expect(date('2024-03-15T14:30:00')).not.toContain(':');
    });

    it('reports unparseable input rather than throwing', () => {
        expect(date('not a date')).toBe('Invalid DateTime');
    });

    it('returns undefined for falsy input', () => {
        expect(date('')).toBeUndefined();
        expect(date(null)).toBeUndefined();
        expect(date(undefined)).toBeUndefined();
    });
});

describe('datetime', () => {
    it('renders an ISO datetime through luxon in medium format', () => {
        expect(datetime('2024-03-15T14:30:00')).toBe(
            DateTime.fromISO('2024-03-15T14:30:00').toLocaleString(DateTime.DATETIME_MED),
        );
    });

    it('includes the time of day, unlike date()', () => {
        const value = '2024-03-15T14:30:00';
        expect(datetime(value)).toContain(':');
        expect((datetime(value) as string).length).toBeGreaterThan((date(value) as string).length);
    });

    it('reports unparseable input rather than throwing', () => {
        expect(datetime('not a date')).toBe('Invalid DateTime');
    });

    it('returns undefined for falsy input', () => {
        expect(datetime('')).toBeUndefined();
        expect(datetime(null)).toBeUndefined();
    });
});
