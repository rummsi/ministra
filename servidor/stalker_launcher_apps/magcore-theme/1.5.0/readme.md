magsdk theme architecture description
=====================================

All themes for magcore platform apps should contains styles for [magsdk](https://www.npmjs.com/package/magsdk) components and special class styles.

###App background

Theme should set background to `body` tag to specify apps background, otherwise it will be transparent.

###Components

This revision of theme architecture standard should contains styles for components:

* mag-component-panel-set
* mag-component-panel
* mag-component-modal
* mag-component-list
* mag-component-layout-list
* mag-component-radio-list
* mag-component-check-list
* mag-component-value-list
* mag-component-footer
* stb-component-button
* spa-component-button
* spa-component-scrollbar
* stb-component-scrollbar
* spa-component-checkbox

All components should be `1.*.*` version.

###Variables access

Since v1.3.0 themes should contains `main.json` file with exported color variables, that may be used in applications code.

###Special classes

Theme should contains special classes for common things for components and developers, who wants to make they non magsdk components or styles looks like the same appearance.

* `.theme-focus` - focus element (such as focus item, in list)
* `.theme-color-text` - special text color
* `.theme-styled-text` - special styled text color
* `.theme-styled-background` - special styled background color
* `.theme-main` - main part of some component (body html node of component)
* `.theme-footer` - class to separate footer from content @deprecated - use '-separator-' classes
* `.theme-header` - class to separate header from content @deprecated - use '-separator-' classes
* `.theme-separator-top` - top separator line
* `.theme-separator-bottom` - bottom separator line
* `.theme-separator-left` - left separator line
* `.theme-separator-right` - right separator line
* `.theme-subhead-color` - color of subhead text
* `.theme-item-editable` - mark some item as editable (e.g. pen icon in theme graphite)
* `.theme-item-more` - mark some item as possible to interact
* `.theme-title-color` - title color
* `.theme-additional-text` - additional text color
* `.theme-color-warning` - warning color (e.g. color of warning icon)
* `.theme-color-success` - success color (e.g. color of success icon)
* `.theme-color-error` - error color (e.g. color of error icon)
* `.theme-icon` - together with special icon class add this icon to html node as `:before` pseudo element
* `.theme-text-disabled` - text that reflects disabled element state
* `.theme-text-password` - password styles for text
* `.theme-counter` - class to draw item counter
* `.theme-h2` - h2 heading
* `.theme-border` - special theme border style
* `.theme-border-top` - only top border
* `.theme-border-bottom` - only bottom border
* `.theme-border-left` - only left border
* `.theme-border-right` - only right border
* `.theme-equalizer-animated` - draw animated equalizer. Html element with this class must contain child div element

###Component dependent classes

#####Padded

All `-component-list*` items should have zero padding (`padding:0;`) by default and special class `padded` add paddings to items.


#####Width2x
`mag-component-modal` have fixed width of 20% and if you want to make your modal 40% you should add `width2x` class.

###Special page layouts

####Progress layout
`Progress layout` - is kind of typical page with big progressbar in center and some content.
It is default html structure to create page with progress layout.
```html
.theme-progress-layout - layout container
    .theme-progress-header - layout header
    .theme-progress-content - page content
    .theme-progressbar - add this class to `-component-scrollbar`
    .theme-progressbar-value - progressbar value
```

###Icons

 List of icons:
 * `theme-icon-rc-f1`
 * `theme-icon-rc-f2`
 * `theme-icon-rc-f3`
 * `theme-icon-rc-f4`
 * `theme-icon-plus`
 * `theme-icon-rc-play-pause`
 * `theme-icon-rc-ok`
 * `theme-icon-rc-info`
 * `theme-icon-rc-menu`
 * `theme-icon-rc-back`
 * `theme-icon-rc-home`
 * `theme-icon-rc-vk`
 * `theme-icon-rc-stop`
 * `theme-icon-rc-previous`
 * `theme-icon-rc-next`
 * `theme-icon-rc-rewind`
 * `theme-icon-rc-forward`
 * `theme-icon-rc-settings`
 * `theme-icon-rc-volume-up`
 * `theme-icon-rc-volume-down`
 * `theme-icon-rc-mute`
 * `theme-icon-rc-aspect`
 * `theme-icon-rc-power`
 * `theme-icon-rc-app`
 * `theme-icon-rc-tv`
 * `theme-icon-rc-refresh`
 * `theme-icon-play`
 * `theme-icon-pause`
 * `theme-icon-previous`
 * `theme-icon-next`
 * `theme-icon-rewind`
 * `theme-icon-forward`
 * `theme-icon-exit`
 * `theme-icon-channel-minus`
 * `theme-icon-channel-plus`
 * `theme-icon-volume`
 * `theme-icon-pip`
 * `theme-icon-mute`
 * `theme-icon-favorite-active`
 * `theme-icon-favorite`
 * `theme-icon-timeshift`
 * `theme-icon-display`
 * `theme-icon-sound`
 * `theme-icon-aspect`
 * `theme-icon-menu`
 * `theme-icon-categories`
 * `theme-icon-equalizer`
 * `theme-icon-sort-az`
 * `theme-icon-search`
 * `theme-icon-settings`
 * `theme-icon-lock`
 * `theme-icon-radio`
 * `theme-icon-radio-active`
 * `theme-icon-sun`
 * `theme-icon-moon`
 * `theme-icon-cloud-sun`
 * `theme-icon-cloud-moon`
 * `theme-icon-rain`
 * `theme-icon-heavy-rain`
 * `theme-icon-snow`
 * `theme-icon-cloud`
 * `theme-icon-cloud-sun-2`
 * `theme-icon-equalizer-0`
 * `theme-icon-equalizer-1`
 * `theme-icon-equalizer-2`
 * `theme-icon-equalizer-3`
 * `theme-icon-equalizer-4`
 * `theme-icon-up`
 * `theme-icon-down`
 * `theme-icon-warning`
 * `theme-icon-filter`
 * `theme-icon-clock`
 * `theme-icon-ok`
 * `theme-icon-cancel`
 * `theme-icon-genres`
 * `theme-icon-teletext`
 * `theme-icon-sublitles`
 * `theme-icon-checkbox`
 * `theme-icon-checkbox-active`
 * `theme-icon-back`
 * `theme-icon-sd-card`
 * `theme-icon-hdd`
 * `theme-icon-usb`
 * `theme-icon-network`
 * `theme-icon-upnp`
 * `theme-icon-file`
 * `theme-icon-folder`
 * `theme-icon-workgroup`
 * `theme-icon-server`
 * `theme-icon-shared-folder`
 * `theme-icon-shortcut`
 * `theme-icon-audio`
 * `theme-icon-image`
 * `theme-icon-video`
 * `theme-icon-text`
 * `theme-icon-stream`
 * `theme-icon-record`
 * `theme-icon-dvb`
 * `theme-icon-iso`
 * `theme-icon-cue`
 * `theme-icon-playlist`
 * `theme-icon-select-all`
 * `theme-icon-move`
 * `theme-icon-edit`
 * `theme-icon-more`
 * `theme-icon-create-folder`
 * `theme-icon-not-allowed`
 * `theme-icon-home`
 * `theme-icon-refresh`
 * `theme-icon-internet`
 * `theme-icon-lan`
 * `theme-icon-stop`
 * `theme-icon-scale`
 * `theme-icon-virtual-mouse`
 * `theme-icon-navigate`
 * `theme-icon-download`
 * `theme-icon-avatar`
 * `theme-icon-monitor`
 * `theme-icon-support`
 * `theme-icon-slideshow`
 * `theme-icon-shuffle`
 * `theme-icon-repeat`
 * `theme-icon-rotate`
 * `theme-icon-rotate-back`
 * `theme-icon-toggle`
 * `theme-icon-toggle-active`
 * `theme-icon-timezone`
 * `theme-icon-ntp-server`
 * `theme-icon-languages`
 * `theme-icon-font-size`
 * `theme-icon-color`
 * `theme-icon-sand-clock`
 * `theme-icon-DVB`
 * `theme-icon-dvb-power-on`
 * `theme-icon-teletext-ratio`
 * `theme-icon-opacity`
 * `theme-icon-update`
 * `theme-icon-rc`
 * `theme-icon-standby`
 * `theme-icon-clear`
 * `theme-icon-reload`
 * `theme-icon-reboot`
 * `theme-icon-linked`
 * `theme-icon-wifi`
 * `theme-icon-multicast`
 * `theme-icon-interface`
 * `theme-icon-hdmi`
 * `theme-icon-screensaver`
 * `theme-icon-screensaver-interval`
 * `theme-icon-brightness`
 * `theme-icon-brightness-standby`
 * `theme-icon-portal`
 * 'theme-icon-beta'
 * 'theme-icon-alarm-clock'
 * 'theme-icon-hardware-acceleration'

####Additional icons classes:
`active` - add this class to set icon as active and add special color
`disabled` - add this class to set icon as disabled add special color

###App dependent classes

####magcore-app-tv
`magcore-app-tv` contains page with unique html page-proofs, so theme's, that want to support this app should contain's special classes for it.
This classes describes view of page with epg programs grid:

* `.theme-epg-grid-main` - main part of epg grid view content
* `.theme-epg-grid-date` - epg grid date background
* `.theme-epg-grid-hourmark` - epg grid hourmark
* `.theme-epg-grid-timeline` - timeline
* `.theme-epg-grid-timemark` - epg timemark
* `.theme-epg-grid-default` - default item in grid
* `.theme-epg-grid-played` - played item in grid
* `.theme-epg-grid-current` - current item in grid
* `.theme-epg-grid-noData` - item with unknown data in grid
* `.theme-epg-grid-focused` - focused item in grid
* `.theme-epg-grid-timeshift` - item in grid with timeshift
* `.theme-epg-grid-arrow-left` - navigate arrow left
* `.theme-epg-grid-arrow-right` - navigate arrow right


Watch icons example [here](https://github.com/magcore/theme-graphite/index.html)
