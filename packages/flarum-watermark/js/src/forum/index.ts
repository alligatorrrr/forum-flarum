import { Watermark } from './components/index';
import { extend } from 'flarum/extend';
import PostStream from 'flarum/components/PostStream';
import app from 'flarum/app';
import Navigation from 'flarum/components/Navigation';

// 动态计算宽度的函数，基于文本内容和字体大小
const calculateDynamicWidth = (text: string, fontSize: number): number => {
  const estimatedCharWidth = fontSize * 0.6; // 每个字符约占字体大小的 60% 宽度
  return text.length * estimatedCharWidth;
};

app.initializers.add('annonny/flarum-watermark', () => {
  var waterMark: any;
  var secretWaterMark: any;

  extend(PostStream.prototype, 'view', function (this: PostStream, vdom: any) {
    if (!secretWaterMark) {
      let currentUserId = "";
      if (app.session.user) {
        currentUserId = currentUserId + app.session.user.id();
      }

      // 生成水印文本
      const blindText = (currentUserId + ' ' + currentUserId + ' 狗厂').repeat(10);

      // 动态计算水印宽度
      const blindFontSize = 70;
      const dynamicWidth = calculateDynamicWidth(blindText, blindFontSize);

      const watermarkConfig = {
        monitor: false,
        blindText: blindText,
        blindFontSize: blindFontSize,
        blindOpacity: 0.004,
        gapX: 100, // 横向间距，适当减小，让水印更紧密
        gapY: 110, // 纵向间距，保持默认或适当调整
        width: dynamicWidth, // 动态计算的宽度
      };

      // 初始化水印
      secretWaterMark = new Watermark(watermarkConfig);
    } else {
      secretWaterMark.show();
    }
  });
});