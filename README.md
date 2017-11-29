# rtc-slider
Just Simple Slider!

[![Build Status](https://travis-ci.org/abhijitrakas/rtc-slider.svg?branch=master)](https://travis-ci.org/abhijitrakas/rtc-slider)
[![Coverage Status](https://coveralls.io/repos/github/abhijitrakas/rtc-slider/badge.svg?branch=master)](https://coveralls.io/github/abhijitrakas/rtc-slider?branch=master)

## Getting Started
Clone it in your WordPress set up 'plugins' directory. Then go to 'Plugins' section and activate plugin. As soon as plugin activated 'Rtc Slider' menu is display below 'Setting' menu in WordPress backend.

### Plugin Set Up
Click on **Rtc Slider** menu option. On the page check at the top near page title there is **Upload Slider Image** button. Click on button and select images from media library or upload new images which you want to see as slider images.

### Add slider in page
After images selected, use **[myslideshow]** shortcode to display slider on page.

### Change images order
Simple drag and drop images up and down to change images order.

### Remove images from slider
On **Rtc Slider** plugin setting page, to the left side of each selected image a checkbox is present. Checked checkbox of image which you want to remove from slider. Then on the top of page, (below page title) 'Select' box is present, select **Remove** option from select box and click on **Apply** button to remove image.

### UnitTest
To run unit test go to plugin folder using terminal and run following command with your database creadentials.
```
bin/install-wp-tests.sh --databaseName --userName --userPassword --host --version
```
Example:
```
bin/install-wp-tests.sh wordpress_test root root localhost latest
```