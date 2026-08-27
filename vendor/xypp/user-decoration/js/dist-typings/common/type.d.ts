import Application from "flarum/common/Application";
import type UserDecorations from "./models/UserDecorations";
import ForumApplication from "flarum/forum/ForumApplication";
import AdminApplication from "flarum/admin/AdminApplication";
import Mithril from "mithril";
export type AllApplication = Application | ForumApplication | AdminApplication;
export interface userElementInfo {
    username?: string;
    id?: number;
    color?: string;
    container?: Mithril.Vnode<any, any>;
    decoration?: UserDecorations;
    decorationId?: number;
}
