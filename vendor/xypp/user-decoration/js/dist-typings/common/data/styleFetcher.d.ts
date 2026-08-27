import UserDecorations from "../models/UserDecorations";
import { AllApplication } from "../type";
export declare class StyleFetcher {
    fetchIntervalId: number;
    fetchId: Record<number, ((result: UserDecorations) => void)[]>;
    cb: (() => void)[];
    app: AllApplication;
    constructor(app: AllApplication);
    done(c: () => any): void;
    static getInstance(): StyleFetcher | null;
    sendFetch(): Promise<void>;
    fetchStyle(id: number | string): Promise<UserDecorations | null>;
    fetchStyleSync(id: number | string): UserDecorations | undefined;
    getApp(): AllApplication;
}
