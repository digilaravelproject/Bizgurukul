import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.dispatchEvent(new CustomEvent('alpine:init'));
Alpine.start();
