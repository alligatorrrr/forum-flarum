import app from 'flarum/common/app';
import { StyleFetcher } from './data/styleFetcher';
import { initDecorationHijack } from './utils/DecorationHijack';
import { applyDecoration, applyDecorationOn, reloadStyleElement } from './utils/DecorationApplier';
import { makeWarpComponent, DecorationWarpComponent } from './utils/DecorationWarpComponent';
export { StyleFetcher, applyDecoration, applyDecorationOn, makeWarpComponent, DecorationWarpComponent, reloadStyleElement } 