import User from "flarum/common/models/User";
export declare function initDecorationHijack(): void;
export declare function initDecorationExtend(): void;
declare function calculateAvatarColor(user: User, avatarUrl: string): void;
export declare const avatarColor: typeof calculateAvatarColor;
export {};
