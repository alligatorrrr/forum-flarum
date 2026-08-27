<?php

namespace Ziven\decorationStore\Console;

use Flarum\Foundation\Paths;
use Illuminate\Console\Scheduling\Event;

use Ziven\decorationStore\Helpers\CommonHelper;

class PublishSchedule{
    public function __invoke(Event $event){
        $settingTimezone = (new CommonHelper)->getSettingTimezone();
        $event->daily()->timezone($settingTimezone);
        $paths = resolve(Paths::class);
        $event->appendOutputTo($paths->storage.(DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'decoration-store.log'));
    }
}
