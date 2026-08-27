/// <reference types="mithril" />
/// <reference types="flarum/@types/translator-icu-rich" />
import Modal from 'flarum/common/components/Modal';
import DecorationBox from '../components/DecorationBox';
export default class OfferDecorationModal extends Modal {
    loading: boolean;
    records: Record<number, string>;
    value: number;
    decorationBox: DecorationBox | null;
    decorationType: string;
    decorationId: string;
    className(): string;
    title(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    content(): JSX.Element;
    onready(): void;
    offer(): Promise<void>;
    change(e: any): void;
}
