import avatar from 'flarum/common/helpers/avatar';
import username from 'flarum/common/helpers/username';
import User from 'flarum/common/models/User';
export function generatePost(user: User) {
    return (<div
        class="PostStream-item"
        data-index="0"
        data-time="2024-06-27T15:05:51.000Z"
        data-number="1"
        data-id="1"
        data-type="comment"
    >
        <article class="CommentPost Post Post--by-actor Post--by-start-user">
            <div>
                <header class="Post-header">
                    <ul>
                        <li class="item-user">
                            <div class="PostUser">
                                <h3 class="PostUser-name">
                                    <a>{avatar(user)}<span class="UserOnline"></span>
                                        {username(user)}</a>
                                </h3>
                            </div>
                        </li>
                    </ul>
                </header>
                <aside class="Post-actions">
                    <ul>
                        <li>
                            <div class="ButtonGroup Dropdown dropdown Post-controls itemCount3">
                                <button
                                    class="Dropdown-toggle Button Button--icon Button--flat"
                                    aria-haspopup="menu"
                                    aria-label="Toggle post controls dropdown menu"
                                    data-toggle="dropdown">
                                    <i
                                        aria-hidden="true"
                                        class="icon fas fa-ellipsis-h Button-icon"
                                    ></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </aside>
            </div>
        </article>
    </div>
    );
}