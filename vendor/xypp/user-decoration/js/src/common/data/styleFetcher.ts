import UserDecorations from "../models/UserDecorations";
import { AllApplication } from "../type";
let StyleFetcherIns: StyleFetcher | null = null;

export class StyleFetcher {
    fetchIntervalId = -1;
    fetchId: Record<number, ((result: UserDecorations) => void)[]> = {};
    cb: (() => void)[] = [];
    app: AllApplication;

    constructor(app: AllApplication) {
        StyleFetcherIns = this;
        this.app = app;
    }
    public done(c: () => any) {
        this.cb.push(c);
    }
    public static getInstance(): StyleFetcher | null { return StyleFetcherIns; }
    public async sendFetch() {
        this.fetchIntervalId = -1;
        const fetchResultHandler = this.fetchId;
        const id = Object.keys(fetchResultHandler).map(e => parseInt(e));
        this.fetchId = {};

        //@ts-ignore
        const fetchResult = await this.app.store.find("user_decoration", { id });
        id.forEach((e) => {
            fetchResultHandler[e].forEach(result => {
                result(this.app.store.getById('user-decorations', e + "") as any);
            })
        })
        this.cb.forEach(cb => {
            cb();
        })
    }
    public fetchStyle(id: number | string): Promise<UserDecorations | null> {
        if (!id) {
            return Promise.resolve(null);
        }
        id = parseInt(id + "");
        const stored = this.app.store.getById('user-decorations', id + "");
        if (stored) return Promise.resolve(stored as any);

        return new Promise<UserDecorations>((resolve) => {
            this.fetchId[id as number] = this.fetchId[id as number] || [];
            this.fetchId[id as number].push(resolve);
            if (this.fetchIntervalId == -1) {
                this.fetchIntervalId = setTimeout(this.sendFetch.bind(this), 400) as any;
            }
        })
    }
    public fetchStyleSync(id: number | string): UserDecorations | undefined {
        return (this.app.store.getById('user-decorations', id + "") as UserDecorations) || undefined;
    }

    public getApp() {
        return this.app;
    }
}