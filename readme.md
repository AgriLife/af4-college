# College of Agriculture and Life Sciences variation of the AgriFlex4 theme
[![Codeship Status for AgriLife/af4-college](https://app.codeship.com/projects/4ae66d50-32cf-0137-114c-6a255f9975f4/status?branch=master)](https://app.codeship.com/projects/332235)

This WordPress plugin should only be used on https://aglifesciences.tamu.edu/, other College of Agriculture and Life Sciences websites, or testing versions of them. No permission is given to install this on any other website, as it contains visual and informational aspects unique to those colleges. You may repurpose code from this repository for your own WordPress development since we use a GPL-2.0+ license.

## WordPress Requirements

1. Genesis theme
2. AgriFlex4 theme: [Download the latest release](https://github.com/agrilife/agriflex4/releases/latest)
3. Advanced Custom Fields Pro plugin
4. PHP 5.6+, tested with PHP 7.2

## Installation

1. [Download the latest release](https://github.com/agrilife/af4-college/releases/latest)
2. Upload the plugin to your site

## Features

* Visual styles unique to College of Agriculture and Life Sciences websites.
* Add a "grid" class name to top level navigation menu items to have its child menu items arranged in a row instead of a single column.
* Add a "grid" and "grid-right" class name to top level navigation menu items to have its child menu items anchored to the right side of the menu item and arranged in a row instead of a single column.
* Add a "grid" and "grid-full" class name to top level navigation menu items to have its child menu items arranged in a row instead of a single column and appear full-width below the navigation menu.
* Departments menu location in header.
* Footer widget areas.

## Development Installation

1. Copy this repo to the desired location.
2. In your terminal, navigate to the plugin location 'cd /path/to/the/plugin'.
3. Run "npm start" to configure your local copy of the repo, install dependencies, and build files for a production environment.
4. Or, run "npm start -- develop" to configure your local copy of the repo, install dependencies, and build files for a development environment.

## Development Notes

When you stage changes to this repository and initiate a commit, they must pass PHP and Sass linting tasks before they will complete the commit step. Release tasks can only be used by the repository's owners.

## Development Tasks

1. Run "grunt develop" to compile the css when developing the plugin.
2. Run "grunt watch" to automatically compile the css after saving a *.scss file.
3. Run "grunt" to compile the css when publishing the plugin.
4. Run "npm run checkwp" to check PHP files against WordPress coding standards.

## Development Requirements

* Node: http://nodejs.org/
* NPM: https://npmjs.org/
* Ruby: http://www.ruby-lang.org/en/, version >= 2.0.0p648
* Ruby Gems: http://rubygems.org/
* Ruby Sass: version >= 3.4.22

