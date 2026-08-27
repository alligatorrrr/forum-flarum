import Mithril, { Vnode } from "mithril";
import UserDecorations from "../models/UserDecorations";
import { userElementInfo } from "../type";
import { StyleFetcher } from "../data/styleFetcher";

/**
 * 在用户界面对象上应用样式
 * @param elementInfo 用户界面元素对象.由各种事件中直接获取包装得到.
 * @param ctx 一般为根component的VNode
 */
export function applyDecoration(elementInfo: userElementInfo, ctx: any) {
    if (!elementInfo.decoration) {
        if (elementInfo.decorationId) {
            elementInfo.decoration = StyleFetcher.getInstance()?.fetchStyleSync(elementInfo.decorationId);
            if (!elementInfo.decoration) {
                StyleFetcher.getInstance()?.fetchStyle(elementInfo.decorationId).then(() => {
                    $(ctx.element).addClass("user-decoration-hijack-wait-reload");
                    m.redraw();
                });
            }
        }
        if (!elementInfo.decoration)
            return;
    }
    if (!elementInfo.container) return;
    applyDecorationOn(elementInfo.container, elementInfo.decoration);
}

type ElementCreateDesc = {
    tag?: string,
    class?: string,
    parent?: string[] | RegExp[] | RegExp | string,
    copy?: string[] | RegExp[] | RegExp | string,
    after?: string | RegExp,
    content?: string,
    [key: string]: any
};

const elementCreations: Record<string, Record<string, ElementCreateDesc>> = {};
const findingCache: Record<string, Record<string, number[] | false>> = {};
const warningSent: Record<string, Record<string, boolean>> = {};
function sendWarning(decId: string, key: string, message: string, Obj: any) {
    if (!warningSent[decId]) warningSent[decId] = {};
    if (!warningSent[decId][key]) {
        warningSent[decId][key] = true;
        console.warn(message, Obj);
    }
}
function isVNodeMatchClass(vnode: Mithril.Vnode<any, any>, className: string | RegExp) {
    if (className instanceof RegExp) {
        if ((className as RegExp).test(vnode.attrs?.className || "")) return true;
    } else if (className == "*") {
        return true;
    } else if ((vnode.attrs?.className || "").includes(className)) {
        return true;
    } else if ((vnode.tag?.toString() as string || "").includes(className)) {
        return true;
    }
    return false;
}
function getVNodeWithCachePath(root: Vnode<any, any>, path: number[], targetClass: string | RegExp) {
    let current = root;
    let currentSelect: number | undefined;
    while ((currentSelect = path.shift()) !== undefined) {
        if (current.children && typeof current.children === "object" && current.children[currentSelect])
            current = current.children[currentSelect] as any;
        else return null;
    }
    if (isVNodeMatchClass(current, targetClass)) {
        return current;
    }
    return null;
}
function getVNodeWithClass(root: Vnode<any, any>, className: string[] | RegExp[], curDep: number, path: number[] | undefined = []) {
    if (root.tag == "#") return;
    let nxtLvl = curDep;
    if (isVNodeMatchClass(root, className[curDep])) {
        nxtLvl++;
        if (nxtLvl == className.length)
            return root;
    }
    if (typeof root.children === "object" && typeof root.children.length === "number") {
        for (let i = 0; i < root.children.length; i++) {
            if (root.children[i]) {
                const ret: any = getVNodeWithClass(root.children[i] as any, className, nxtLvl, path);
                if (ret) {
                    path?.unshift(i);
                    return ret;
                }
            }
        }
    }
    return null;
}
function getElementAuto(element: any, decId: any, key: any, className: string | RegExp) {
    let ret = null;
    if (findingCache[decId][key]) {
        ret = getVNodeWithCachePath(element, findingCache[decId][key] as number[], className);
        if (!ret) findingCache[decId][key] = false;
    }
    if (!ret) {
        const path: number[] = [];
        if (findingCache[decId][key] === false)
            ret = getVNodeWithClass(element, className as any, 0, undefined);
        else {
            ret = getVNodeWithClass(element, className as any, 0, path);
            if (ret) {
                findingCache[decId][key] = path;
            }
        }
    }
    return ret;
}
/**
 * 在容器元素上应用样式
 * @param element 容器元素
 * @param decoration 用户装饰样式对象
 */
export function applyDecorationOn(element: Mithril.Vnode<any>, decoration: UserDecorations) {
    const decId: string = decoration.id() as string;
    if (!elementCreations[decId as string]) {
        let styleText = decoration.style() as string;
        elementCreations[decId as string] = {};
        const matchGene = styleText.matchAll(/\.(element-[^\[\{]*)((?:\[[a-zA-Z0-9]+=.+\])*)[^\[]/ig);
        let res = matchGene.next();
        while (!res.done) {
            const ar = res.value as string[];
            const id = ar[1] as string;
            const regExt = ar[2] as string;
            const creation: ElementCreateDesc = {};
            if (regExt) {
                const subGen = regExt.matchAll(/\[([a-zA-Z0-9]+)=([^\]]+)\]/ig);
                let res = subGen.next();
                while (!res.done) {
                    const ar = res.value as string[];
                    const argName = ar[1] as string;
                    const argValue = (ar[2] as string).trim();
                    if ((argValue.startsWith('"') && argValue.endsWith('"')) || argValue.startsWith("'") && argValue.endsWith("'")) {
                        creation[argName] = argValue.slice(1, -1);
                    } else if (argValue.startsWith("/") && argValue.includes("/", 1)) {
                        const tmp = argValue.split("/");
                        creation[argName] = new RegExp(tmp[1], tmp[2]);
                    } else creation[argName] = argValue;

                    res = subGen.next()
                }

            }
            if (!creation.tag || creation.tag == "#") {
                creation.tag = "span";
            }
            if (creation.copy) {
                if (typeof creation.copy === "string") {
                    creation.copy = creation.copy.split(" ");
                } else creation.copy = [creation.copy as any];
            }
            if (creation.parent) {
                if (typeof creation.parent === "string") {
                    creation.parent = creation.parent.split(" ");
                } else creation.parent = [creation.parent as any];
            }
            elementCreations[decId as string][id] = creation;
            res = matchGene.next();
        }
        styleText = styleText.replace(/(\.element-[^\[]*)(?:\[[a-zA-Z0-9]*=.*\])*([^\[])/ig, "$1$2");
        styleText = styleText.replace(/\.base/g, `.user-decoration-${decId}`);
        const ctr = $("<style>").attr("id", `user-decoration-${decId}`);
        ctr.html(styleText);
        $("head").append(ctr);
    }
    if (!findingCache[decId]) findingCache[decId] = {};
    const toApply: any[] = [];
    Object.keys(elementCreations[decId as string]).forEach(key => {
        const args = elementCreations[decId][key];
        let parent = null;
        if (args.parent) {
            parent = getElementAuto(element, decId, key, args.parent as any);
            if (!parent) {
                sendWarning(decId, key, "[DecorationApplier] Parent element is not found.", { createSpec: args, element });
                return;
            }
        } else parent = element;
        if (!parent.children || typeof parent.children !== "object" || typeof parent.children?.length !== "number") {
            sendWarning(decId, key, "[DecorationApplier] Parent element is not a fragment.", { createSpec: args, parent, element });
            return;
        }
        let newElement = null;
        if (args.copy) {
            const copyTarget = getElementAuto(element, decId, key, args.copy as any);
            if (copyTarget) {
                newElement = $.extend(true, {}, copyTarget);
            }
            if (!newElement) {
                sendWarning(decId, key, "[DecorationApplier] To copy element not found", { createSpec: args, element });
                return;
            }
            if (args.class) {
                newElement.attrs.className = (newElement.attrs.className || "") + " " + args.class;
            }
            newElement.attrs.className = (newElement.attrs.className || "") + " " + key;
        } else {
            // 非拷贝，添加新的元素
            newElement = {
                tag: args.tag,
                states: undefined,
                attrs: {
                    className: (args.class || "") + " " + key,
                    style: args.style || "",
                },
                children: [
                    {
                        tag: "#",
                        children: args.content || "",
                        attrs: {},
                        states: undefined
                    }
                ]
            };
        }
        let afterIndex = (element.children as any[]).length - 1;
        if (args.after && element.children) {
            for (let i = 0; i < (element.children as any[]).length; i++) {
                if (isVNodeMatchClass((element.children as any)[i], args.after)) {
                    afterIndex = i;
                    break;
                }
            }
        }
        toApply.push([parent, afterIndex, newElement]);
    });
    toApply.forEach(element => {
        (element[0].children as any[]).splice(element[1] + 1, 0, element[2]);
    });
    if (!new RegExp(`( |^)user-decoration-${decId}( |$)`).test(element.attrs.className))
        element.attrs.className += ` user-decoration-${decId}`;
    if (!/( |^)has-user-decoration( |$)/.test(element.attrs.className))
        element.attrs.className += ` has-user-decoration`;
}

export function reloadStyleElement(id: number) {
    if (elementCreations[id]) {
        $(`#user-decoration-${id}`).remove();
        delete elementCreations[id];
        m.redraw();
    }
}