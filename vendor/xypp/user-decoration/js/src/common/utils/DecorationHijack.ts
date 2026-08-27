import Component from "flarum/common/Component";
import { extend, override } from "flarum/common/extend";
import User from "flarum/common/models/User";
import { StyleFetcher } from "../data/styleFetcher";
import { userElementInfo } from "../type";
import { applyDecoration, applyDecorationOn } from "./DecorationApplier";
import computed from 'flarum/common/utils/computed';
import stringToColor from 'flarum/common/utils/stringToColor';
import ColorThief, { Color } from "color-thief-browser";
import Mithril, { Vnode } from "mithril";
import Post from "flarum/forum/components/Post";
import UserCard from "flarum/forum/components/UserCard";
import UsersSearchResults from "flarum/forum/components/UsersSearchSource"
import { DecorationWarpComponent, makeWarpComponent } from "./DecorationWarpComponent";
import username from "flarum/common/helpers/username";
import highlight from "flarum/common/helpers/highlight";

var globalUserDecorationHijackIid = 0;
var noDecorateClassFilter: string[] | null = null;
function loadNoDecorateClassFilter() {
    if (noDecorateClassFilter !== null) return;
    noDecorateClassFilter = (StyleFetcher.getInstance()?.getApp().forum?.attribute<string>("no_decorate_class_filter") || "").split("\n");
    noDecorateClassFilter = noDecorateClassFilter.map(v => v.trim()).filter(v => v !== "");
}
function usernameHijack() {
    return StyleFetcher.getInstance()?.getApp().forum?.attribute("username_hijack");
}
function avatarHijack() {
    return StyleFetcher.getInstance()?.getApp().forum?.attribute("avatar_hijack");
}
let timer: any = 0;
function injectPositionCss() {
    if (timer) return;
    timer = setTimeout(injectPositionCssAfterTick, 20);
}
function injectPositionCssAfterTick() {
    timer = 0;
    const ctr = $(`.decoration-container`);
    ctr.each((_, el) => {
        const ctr = $(el);
        if (ctr.length && !["absolute", "fixed", "relative"].includes(window.getComputedStyle(ctr[0]).position)) {
            ctr.css("position", "relative");
        }
        // User card and post component has controls a large amount of elements and setting z-index may cause many problems.
        if (ctr.length && !ctr.hasClass("Post") && !ctr.hasClass("UserCard") && window.getComputedStyle(ctr[0]).zIndex === "auto") {
            ctr.css("z-index", "0");
        }
        if (ctr.hasClass("before-positioning")) {
            ctr.removeClass("before-positioning");
        }
    });
}
export function initDecorationHijack() {
    const originalUserAvatar = User.prototype.avatarUrl;

    User.prototype.realAvatarUrl = originalUserAvatar;

    User.prototype.hijackColor = function () {
        return computed<string, User>('displayName', 'realAvatarUrl', 'avatarColor', (displayName, avatarUrl, avatarColor) => {
            if (avatarColor) {
                return `rgb(${(avatarColor as Color).join(', ')})`;
            } else if (avatarUrl) {
                calculateAvatarColor(this, avatarUrl as string);
                return '';
            }
            return '#' + stringToColor(displayName as string);
        }).call(this);
    };
    User.prototype.color = User.prototype.hijackColor;
    
    override(User.prototype, "avatarUrl", function () {
        if (!avatarHijack()) return originalUserAvatar.call(this);

        let color = this.hijackColor();
        if (color && !!(color['charAt'])) {
            color = color.replace(/#/g, '@');
        }
        const encodedUserInfo: string = JSON.stringify({
            decorationId: this.data.attributes?.avatar_decoration,
            username: this.username(),

            displayName: this.realDisplayName(),
            id: this.id(),
            color
        });

        return (originalUserAvatar.call(this) || "").split("#").pop() + "#" + encodedUserInfo;
    });


    const originalUserName = User.prototype.username;

    User.prototype.realUserName = originalUserName;

    User.prototype.realDisplayName = User.prototype.displayName;

    override(User.prototype, "displayName", function (orgUserName) {
        if (!usernameHijack()) return orgUserName();
        return makeWarpComponent({
            tag: "span",
            attrs: {
                className: "username-container decoration-container before-positioning",
                user: this
            }
        }, orgUserName(), { user: this }, "username-text");
    });


    extend(Component.prototype, ['oninit'], function () {
        if (!usernameHijack() && !avatarHijack()) return;
        (this as any).userDecorationHijackIid = globalUserDecorationHijackIid++;
        (this as any).originalView = this.view.bind(this);
        this.view = hijackViewHandler.bind(this);
        (this as any).originalOnBefUp = this.onbeforeupdate.bind(this);
        this.onbeforeupdate = hijackOnBeforeUpdate.bind(this);
    });


    // This function should be save and not to control it.
    extend(Component.prototype, ['onupdate', "oncreate"], async function () {
        injectPositionCss();
    });
    console.log("Decoration Hijack loaded");
}

export function initDecorationExtend() {
    override(Post.prototype, "view", function (o, ...a) {
        this.userDecorationHijackIid = this.userDecorationHijackIid || (globalUserDecorationHijackIid++);
        const tree: any = o(a);
        const user = this.attrs.post?.user();
        // For deleted user/anonymous user.
        if (!user) {
            return tree;
        }
        tree.attrs['data-userDecorationHijackIid'] = this.userDecorationHijackIid;
        tree.attrs.className = (tree.attrs.className || "") + " decoration-container before-positioning"
        const infoElem: userElementInfo = {
            username: user.realUserName(),
            container: tree,
            id: user.id(),
            decorationId: user.attribute("post_decoration")
        }
        if (this.attrs.decoration_id) infoElem.decorationId = this.attrs.decoration_id;
        applyDecoration(infoElem, this);
        return tree;
    })
    override(UserCard.prototype, "view", function (this: UserCard, o: any, ...a: any) {
        this.userDecorationHijackIid = this.userDecorationHijackIid || (globalUserDecorationHijackIid++);
        const tree: any = o(a);

        tree.attrs['data-userDecorationHijackIid'] = this.userDecorationHijackIid;
        tree.attrs.className = (tree.attrs.className || "") + " decoration-container before-positioning"

        const user: User = (this.attrs as any).user;
        const infoElem: userElementInfo = {
            username: user.realUserName(),
            container: tree,
            id: parseInt(user.id()!),
            decorationId: user.attribute("card_decoration")
        }
        // @ts-ignore
        if (this.attrs.decoration_id) infoElem.decorationId = this.attrs.decoration_id;
        applyDecoration(infoElem, this);
        return tree;
    })

    /**
     * 搜索框兼容
     */
    override(UsersSearchResults.prototype, "view", function (this: UsersSearchResults, o, a: string) {
        let nodes = o(a);
        for (let i = 1; i < nodes.length; i++) {
            const children = nodes[i];
            const child = children.children[0];
            const uid: number = parseInt((children.attrs["data-index"] as string || "").substring(5));
            const user: User | undefined = StyleFetcher.getInstance()?.getApp()?.store.getById<User>("users", uid + "");
            if (!user) return;
            const inner = highlight(user.displayName(), a);
            const node: DecorationWarpComponent = new DecorationWarpComponent({
                children: [inner],
                tag: "span",
                attrs: {
                    className: "username-container",
                    user: this
                }
            }, user.displayName(), { user }, "username-text");
            child.children[1].children = [node];
        }
        return nodes;
    });
    if (flarum.extensions["flarum-mentions"]) {
        override((StyleFetcher.getInstance()?.getApp() as any)
            .mentionFormats.get("@").mentionables.find((e: any) => e.name == "u")
            .prototype, "suggestion", (function (o: any, user: User, type: string) {
                const nodes = o(user, type);
                if (nodes.tag != '[') return nodes;
                const inner = highlight(user.displayName(), type);
                const innerNode: DecorationWarpComponent = new DecorationWarpComponent({
                    children: [inner],
                    tag: "span",
                    attrs: {
                        className: "username-container",
                        user: user
                    }
                }, user.displayName(), { user }, "username-text");
                nodes.children[1].children = [innerNode];
                return nodes;
            }) as any)
    }
}
function hijackOnBeforeUpdate(this: Component, ...a: any) {

    const injected = this.$(".user-decoration-hijack-wait-reload").length !== 0 || $(this.element).hasClass("user-decoration-hijack-wait-reload");

    $(this.element).removeClass("user-decoration-hijack-wait-reload");

    if (this.originalOnBefUp.apply(this, a) === false) {
        if (injected) {
            return;
        }
        return false;
    }
    return;
}
function hijackViewHandler(this: Component, vnode: any) {

    const vnodeTree = (this as any).originalView(vnode);
    if (!vnodeTree) return vnodeTree;
    if (!noDecorateClassFilter) loadNoDecorateClassFilter();
    if (vnodeIsAvatar(vnodeTree)) {

        return createWrappedAvatar(vnodeTree, this, false);
    }
    if (vnodeIsUsername(vnodeTree)) {

        return createWrappedUserName(vnodeTree, this, false);
    }
    else {

        hijackView(null, vnodeTree, vnode, this, false);
    }
    return vnodeTree;
}
function hijackView(parent: any, root: any, stopAt: Mithril.Vnode<any>, ctx: any, noDecorate: boolean) {
    if (root === stopAt) return;
    if (!noDecorate && noDecorateClassFilter?.length) {
        ((root.attrs?.className || "") as string).split(" ").forEach((element: string) => {
            if (noDecorateClassFilter!.includes(element)) {
                noDecorate = true;
            }
        });
    }
    if (typeof root === "object" && root['forEach']) {
        root.forEach((child: any) => {
            child && hijackView({ children: root }, child, stopAt, ctx, noDecorate);
        });
    }
    if (parent && vnodeIsAvatar(root)) {
        parent.children = (parent.children as Mithril.Vnode<any>[]).map((child) => {
            if (child === root) {
                return createWrappedAvatar(child, ctx, noDecorate);
            } else return child;
        });
    } else if (parent && vnodeIsUsername(root)) {
        parent.children = (parent.children as Mithril.Vnode<any>[]).map((child) => {
            if (child === root) {
                return createWrappedUserName(root, ctx, noDecorate);
            } else return child;
        });
    } else if (typeof root.children === 'object' && root.children['forEach']) {
        root.children.forEach((child: any) => {
            child && hijackView(root, child, stopAt, ctx, noDecorate);
        });
    }
}
function vnodeIsAvatar(vnode: any): boolean {
    return vnode && vnode.tag == "img" && vnode.attrs.className?.includes("Avatar") && /( |^)Avatar( |$)/.test(vnode.attrs.className) && (vnode.attrs as any).src;
}
function vnodeIsUsername(vnode: any): boolean {
    return vnode && vnode instanceof DecorationWarpComponent && ((vnode.attrs.className || "") + "").includes("username-container");
}
function createWrappedAvatar(vnode: Mithril.Vnode<any, any>, ctx: any, noDecorate: boolean) {
    const attrData = vnode.attrs.src.split("#");
    if (attrData.length != 2) return vnode;
    let toWarp: Mithril.Vnode<any, any> = vnode
    const userInfo: userElementInfo = JSON.parse(attrData.pop()!);
    const avatarUrl = attrData.shift();
    if (!avatarUrl) {
        toWarp.tag = "span";
        toWarp.children = [{
            tag: "#",
            children: userInfo.username?.charAt(0),
            state: undefined,
            attrs: {}
        }];
        let color = userInfo.color;
        if (color && !!(color['charAt'])) {
            color = color.replace(/@/g, "#");
        }
        if (color) {
            toWarp.attrs.style = (toWarp.attrs.style ?? "") + `;--avatar-bg: ${color};`;
        }
    }
    toWarp.attrs.src = avatarUrl;

    if (noDecorate) return toWarp;

    const ctr: Mithril.Vnode<any, any> = {
        tag: "span",
        attrs: {
            "data-ctr": "avatar"
        },
        state: undefined,
        children: [toWarp]
    };
    ctr.attrs.style = toWarp.attrs.style;
    ctr.attrs.className = (toWarp.attrs.className ?? "") + " Avatar-container decoration-container before-positioning";
    ctr.attrs['data-userDecorationHijackIid'] = ctx.userDecorationHijackIid;

    if (ctx.appendRelative) {
        ctr.attrs.style = (ctr.attrs.style ?? "") + ";position:relative;"
    }
    toWarp.attrs.className = "";
    userInfo.container = ctr;
    applyDecoration(userInfo, ctx);
    return ctr;
}
function createWrappedUserName(vnode: any, ctx: any, noDecorate: boolean) {
    const user = vnode.data.user;
    if (noDecorate) return username(user);
    const userInfo: userElementInfo = {
        decorationId: user.attribute("name_decoration"),
        username: user.attribute("username"),
        container: vnode
    };
    applyDecoration(userInfo, ctx);
    return vnode;
}
function calculateAvatarColor(user: User, avatarUrl: string) {
    const image = new Image();
    image.addEventListener('load', function (this: HTMLImageElement) {
        try {
            const colorThief = new ColorThief();
            // @ts-ignore
            user.avatarColor = colorThief.getColor(this);
        } catch (e) {
            // Completely white avatars throw errors due to a glitch in color thief
            // See https://github.com/lokesh/color-thief/issues/40
            if (e instanceof TypeError) {
                // @ts-ignore
                user.avatarColor = [255, 255, 255];
            } else {
                throw e;
            }
        }
        user.freshness = new Date();
        m.redraw();
    });
    image.crossOrigin = 'anonymous';
    image.src = avatarUrl ?? '';
}
export const avatarColor = calculateAvatarColor;