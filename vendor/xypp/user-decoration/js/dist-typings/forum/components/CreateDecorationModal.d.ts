/// <reference types="mithril" />
/// <reference types="flarum/@types/translator-icu-rich" />
import Modal from 'flarum/common/components/Modal';
import UserDecorations from '../../common/models/UserDecorations';
export default class CreateDecorationModal extends Modal {
    loading: boolean;
    decorationId: string;
    decoration: UserDecorations | null;
    className(): string;
    title(): import("@askvortsov/rich-icu-message-formatter").NestedStringArray;
    content(): JSX.Element;
    onready(): void;
    onsubmit(e: any): Promise<void>;
    delete(): Promise<void>;
}
