import Mithril from "mithril";
import UserDecorations from "../models/UserDecorations";
import { userElementInfo } from "../type";
/**
 * 在用户界面对象上应用样式
 * @param elementInfo 用户界面元素对象.由各种事件中直接获取包装得到.
 * @param ctx 一般为根component的VNode
 */
export declare function applyDecoration(elementInfo: userElementInfo, ctx: any): void;
/**
 * 在容器元素上应用样式
 * @param element 容器元素
 * @param decoration 用户装饰样式对象
 */
export declare function applyDecorationOn(element: Mithril.Vnode<any>, decoration: UserDecorations): void;
export declare function reloadStyleElement(id: number): void;
