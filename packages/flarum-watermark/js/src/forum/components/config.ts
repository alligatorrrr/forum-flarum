import { WatermarkOptions } from './types';

export const defaultOptions: Partial<WatermarkOptions> = {
  gapX: 450,
  gapY: 110,
  offsetLeft: 50,
  offsetTop: 100,
  width: 220,
  height: 80,
  opacity: 0.2,
  rotate: 0,
  fontSize: 60,
  fontStyle: 'normal',
  fontVariant: 'normal',
  fontWeight: '1000',
  fontColor: '#666',
  fontFamily: 'sans-serif',
  textAlign: 'center',
  textBaseline: 'alphabetic',
  monitor: true,
  zIndex: 9999,
  mode: 'interval',
  pack: true,
  blindFontSize: 60,
  blindOpacity: 0.004,
    // 设置默认水印文本
  text: "小狗梦工厂",
};

/** 用于标记是否需要保护 */
export const attributeNameTag = 'data-secret-ann-tag';

export const observeOptions = {
  childList: true,
  subtree: true,
  attributeFilter: ['style', 'class', attributeNameTag],
};
