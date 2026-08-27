import app from 'flarum/forum/app';
import { extend, override } from 'flarum/common/extend';
import CommentPost from 'flarum/forum/components/CommentPost';
import Post from 'flarum/common/models/Post';
import Model from 'flarum/common/Model';
import MediaInfo from './models/MediaInfo';
// We provide our extension code in the form of an "initializer".
// This is a callback that will run after the core has booted.
app.initializers.add('xypp/acgembed-rr', function (app) {
    // @ts-ignore
    Post.prototype.MediaInfo = Model.hasMany('media-infos');
    app.store.models['media-infos'] = MediaInfo;
    function enableEmbedElem(iframe: JQuery<HTMLElement>, cover: JQuery<HTMLElement>) {
        $(".acgembed-rr-cover.hide").parent().find("iframe").each((_, elem) => {
            $(elem).attr("src", $(elem).attr("data-poster") ?? "");
            $(elem).addClass("acgembed-covered");
        });
        $(".acgembed-rr-cover.hide").fadeIn();
        $(".acgembed-rr-cover.hide").removeClass("hide");
        cover.fadeOut();
        cover.addClass("hide");
        iframe.attr("src", iframe.attr("data-src") ?? "");
        iframe.removeClass("acgembed-covered");
    }

    extend(CommentPost.prototype, ["oncreate","onupdate"], function () {
        const t_media_infos = (this.attrs.post as any).MediaInfo() as MediaInfo[];
        if (!t_media_infos || !t_media_infos.length) return;
        const mediaInfoRec: Record<string, MediaInfo> = {};
        t_media_infos.forEach((media_info) => {
            mediaInfoRec[media_info.source() + ":" + media_info.url()] = media_info;
        });
        $(`.PostStream-item[data-id=${this.attrs.post.id()}]`).find(".acgembed-rr-template").each((i, elem) => {
            if ($(elem).hasClass("init")) {
                return;
            }
            $(elem).addClass("init");
            try {
                const content = (elem as unknown as HTMLTemplateElement)?.content?.firstChild?.cloneNode(true);
                const _info = (elem as unknown as HTMLTemplateElement)?.content?.querySelector("a")?.cloneNode(true);
                if (!content || !_info || !$(_info)) return;
                const infoObj: MediaInfo = mediaInfoRec[$(_info).html() + ":" + $(_info).attr("href")];
                const info = {
                    title: (app.translator.trans("xypp-acgembed-rr.forum.unknown-title", [$(_info).html() as string] as any) as string[]).join(""),
                    desc: app.translator.trans("xypp-acgembed-rr.forum.unknown") as string,
                    poster: ""
                }
                if (infoObj) {
                    info.title = infoObj.title() as string;
                    info.desc = (infoObj.desc() as string);
                    info.poster = infoObj.poster() as string;
                }
                const embedPlayer = $(content);
                const iframe = embedPlayer.find("iframe");
                iframe.attr("data-src", iframe.attr("src") ?? "");
                iframe.attr("data-poster", info.poster ?? "about:blank");
                iframe.attr("src", info.poster ?? "about:blank");
                iframe.addClass("acgembed-covered");
                embedPlayer.insertAfter(elem);
                const coverElem = $("<div></div>");
                coverElem.addClass("acgembed-rr-cover");
                const bgHTML = info.poster && `<div class='acgembed-rr bg' style='background-image:url("${info.poster}")'></div>`
                coverElem.html(`${bgHTML}<div class='acgembed-rr title'></div><div class='acgembed-rr desc'></div><div class='start'>${app.translator.trans("xypp-acgembed-rr.forum.play")}</div>`)
                coverElem.find(".title").text(info.title);
                coverElem.find(".desc").text(info.desc);
                coverElem.insertAfter(iframe);
                coverElem.on("click", () => {
                    enableEmbedElem($(iframe), $(coverElem));
                });
            } catch (e) {
                console.log(e);
            }
        });
    });
});