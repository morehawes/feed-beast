[View Example &raquo;](https://www.morehawes.ca/trips/arizona-2024/)

Display _live_ Garmin inReach <a href="https://support.garmin.com/?faq=p2lncMOzqh71P06VifrQE7">MapShare</a> data on your WordPress site with a simple Shortcode:

```
[inreach-mapshare mapshare_identifier="your_identifier"]
```

![Map embedded in WordPress page](https://raw.githubusercontent.com/morehawes/inreach-mapshare/staging/src/img/screenshot-1.png)

[View Example &raquo;](https://www.morehawes.ca/trips/arizona-2024/)

By default only your most recent location is displayed. To display more data, Start and end dates can be provided through the Shortcode like this:

```
[inreach-mapshare mapshare_identifier="your_identifier" mapshare_date_start="2022-06-15T00:00" mapshare_date_end="2022-07-31T23:59"]
```

You can display a GeoJSON Route accessible via a URL like this:

```
[inreach-mapshare mapshare_route_url="https://www.example.com/my-route.geojson" mapshare_identifier="your_identifier" mapshare_date_start="2022-06-15T00:00"]
```

The MapShare feature must be enabled in the <a href="https://explore.garmin.com/Social">Social</a> tab of your Garmin Explore account.

Features:

- Embed an interactive Map and timeline containing track points and messages shared with your MapShare page.
- Display your planned route using the Shortcode (URL accessible GeoJSON supported).
- Each point displayed contains the following information:
  - Time UTC
  - Time (local)
  - Event (e.g. a message, or track point)
  - Text (for messages)
  - Latitude
  - Longitude
  - Elevation
  - Velocity
  - Valid GPS Fix
- The plugin updates your MapShare every 15 minutes and is cached to improve performance (adjustable in Settings).
- Use the in-built demo to preview how your MapShare will display (`[inreach-mapshare mapshare_identifier="demo"]`).
- Customise in WP Admin > Settings > inReach MapShare.
- Appearance settings:
  - Basemap - <a href="https://www.openstreetmap.org/fixthemap">OpenStreetMap</a> by default, lots of <a href="https://leaflet-extras.github.io/leaflet-providers/preview/">other providers</a> supported.
  - Colour - pick a colour to suit your theme.
  - Icons - use SVG images as icons.
- Content is responsive, and adapts to suit devices both small and large.
- Customise content with your own CSS rules.
- Translation ready :)

If you experience any issues with the plugin, ensure that your MapShare page (i.e. share.garmin.com/[your_identifier]) is displaying data. This is important - this plugin can only display data available to your MapShare.

> [!CAUTION]
> Even if you have a MapShare password set, this plugin simply uses it to request your data; it does not protect it from being viewed. You are responsible for <a href="https://wordpress.org/support/article/using-password-protection/">protecting access</a> if needed.

Please report issues or make suggestions by creating a <a href="https://github.com/opengis/inreach-mapshare/issues">GitHub issue</a>.

> [!NOTE]
> This plugin is free, open-source software and is not maintained by Garmin. Data is requested from the Garmin <a href="https://support.garmin.com/?faq=tdlDCyo1fJ5UxjUbA9rMY8">inReach KML Feeds</a> service.

## Installation

<!-- 1. [Download the plugin (.zip)](https://github.com/OpenGIS/inreach-mapshare/archive/refs/heads/master.zip). -->

1. [Download the plugin (.zip)](https://github.com/OpenGIS/inreach-mapshare/archive/refs/tags/2.0.0.zip).
2. In your WordPress Admin, go to Plugins > Add New Plugin.
3. Click the "Upload Plugin" button.
4. Upload the `inreach-mapshare.zip` file.
5. Activate the plugin.
6. Configure the plugin in WP Admin > Settings > inReach MapShare.

## Configuration

- Ensure MapShare is enabled in the <a href="https://explore.garmin.com/Social">Social</a> tab of your Garmin Explore account.
- Take note of your unique MapShare Address (i.e. share.garmin.com/[your_identifier]). If you have set a password to protect your MapShare page, you will also need to take note of this.
- Visit your MapShare address in your browser and verify that there is MapShare data (i.e. a message or track point) displaying. Any test message <a href="https://support.garmin.com/?faq=p2lncMOzqh71P06VifrQE7">shared with your MapShare page</a> will work.
- With this plugin is activated, go to WP Admin > Settings > inReach MapShare.
- Enter your MapShare address and submit.
- You should see your most recent location, or multiple locations if you supplied a date range.
- Add the provided Shortcode anywhere Shortcodes are supported to display the content on your site.

### Development

> [!NOTE]
> To develop locally you will need to have both Node.js and NPM [installed](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm).

[Vite](https://vitejs.dev/) is used to run the build script, which compiles the JavaScript and CSS and performs some other tasks.

```bash
# Install dependencies
npm install

# Run build script (will watch for changes)
npm run build
```

Pull requests are welcome!

## Frequently Asked Questions

### What is an inReach device?

<a href="https://discover.garmin.com/inreach/personal/">These</a> are small handheld "satellite communicators" which allow users (with an active <a href="https://www.garmin.com/p/837461">subscription</a>) the ability to send tracking and text/email messages from remote areas, unserved by other means of communication.

### What is MapShare? How do I set it up?

<a href="https://support.garmin.com/?faq=p2lncMOzqh71P06VifrQE7">MapShare</a> is an inReach feature which allows friends or loved ones the ability follow progress in real time online.

### What does this plugin Do?

This plugin requests data from your MapShare page and embeds it anywhere Shortcodes are supported.

### What are the KML Extended Data Fields?

| Name             | Description                                                                                           | Example                   |
| ---------------- | ----------------------------------------------------------------------------------------------------- | ------------------------- |
| ID               | Garmin internal ID for the event                                                                      | 868926                    |
| Time UTC         | US-formatted version of the event timestamp as UTC                                                    | 5/2/2020 6:01:30 AM       |
| Time             | US-formatted version of the event timestamp in the preferred time zone of the account owner           | 5/2/2020 9:01:30 AM       |
| Name             | First and last name of the user assigned to the device that sent the message                          | Joe User                  |
| Map Display Name | Map Display Name for this user. This field is editable by the user in their Account or Settings page. | Joe the inReach User      |
| Device Type      | The hardware type of the device in use                                                                | inReach 2.5               |
| IMEI             | The IMEI of the device sending the message                                                            | 300000000000000           |
| Incident ID      | The ID of the emergency event if there is one.                                                        | 1234                      |
| Latitude         | Latitude in degrees WGS84 where negative is south of the equator                                      | 43.790485                 |
| Longitude        | Longitude in degrees WGS84 where negative is west of the Prime Meridian                               | -70.192015                |
| Elevation        | Value is always meters from Mean Sea Level                                                            | 120.39 m from MSL         |
| Velocity         | Ground speed of the device. Value is always in kilometers per hour.                                   | 1.0 km/h                  |
| Course           | Approximate direction of travel of the device, always in true degrees.                                | 292.50° True              |
| Valid GPS Fix    | True if the device has a GPS fix. This not a measure of the quality of GPS fix.                       | True                      |
| In Emergency     | True if the device is in SOS state.                                                                   | False                     |
| Text             | Message text, if any, in Unicode                                                                      | I am doing good!          |
| Event            | The event log type. See table below under Event Log Types                                             | Tracking Message Received |

## Screenshots

### 1. Map embedded in WordPress page

![Map embedded in WordPress page](https://raw.githubusercontent.com/morehawes/inreach-mapshare/staging/src/img/screenshot-1.png)

### 2. Responsive design shown on small screen

![Responsive design shown on small screen](https://raw.githubusercontent.com/morehawes/inreach-mapshare/staging/src/img/screenshot-2.png)

### 3. Responsive design shown on large screen

![Responsive design shown on large screen](https://raw.githubusercontent.com/morehawes/inreach-mapshare/staging/src/img/screenshot-3.png)

### 4. Shortcode Generator (WP Admin > Settings > inReach MapShare)

![Shortcode Generator (WP Admin > Settings > inReach MapShare)](https://raw.githubusercontent.com/morehawes/inreach-mapshare/staging/src/img/screenshot-4.png)

### 5. Appearance Options (WP Admin > Settings > inReach MapShare)

![Appearance Options (WP Admin > Settings > inReach MapShare) ](https://raw.githubusercontent.com/morehawes/inreach-mapshare/staging/src/img/screenshot-5.png)

### 6. Advanced Options (WP Admin > Settings > inReach MapShare)

![Advanced Options (WP Admin > Settings > inReach MapShare)](https://raw.githubusercontent.com/morehawes/inreach-mapshare/staging/src/img/screenshot-6.png)
