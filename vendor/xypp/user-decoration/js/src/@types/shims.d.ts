import Post from 'flarum/common/models/Post';
import User from 'flarum/common/models/User';
declare module 'flarum/common/models/User' {
    export default interface User {
        hijackColor(): string;
        realUserName(): string;
        realDisplayName(): string;
        realAvatarUrl() : string|null;
    }
}

declare module 'flarum/common/Component' {
    export default interface Component {
        userDecorationHijackIid: number;
        originalOnBefUp(any:any): boolean;
    }
}