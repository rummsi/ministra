#!/usr/bin/env node

/**
 *
 * @author Aleynikov Boris <alynikov.boris@gmail.com>.
 */

'use strict';

/*eslint-disable*/

var path      = require('path'),
    themePath = path.join(__dirname, '..', '..', '..'),
    basePath  = path.join(__dirname, '..'),
    sass      = require('node-sass'),
    fs        = require('fs'),
    chokidar  = require('chokidar'),
    TYPE_COLOR  = 'color',
    TYPE_NUMBER = 'number',
    TYPE_STRING = 'string',
    resolutions = [480, 576, 720, 1080],
    command   = process.argv[2],
    flag      = process.argv[3];


switch ( command ) {
    case 'build':
        buildAll();
        break;
    case '480':
    case '576':
    case '720':
    case '1080':
        buildResolution(command);
        break;
    case 'watch':
        startWatch();
        break;
    case 'icons':
    case 'icons-module':
        buildIconJson(command);
        break;
    default:
        buildVarsSCSS();
        break;
}


function buildAll () {
    buildVarsSCSS();
    resolutions.forEach(function ( res ) {
        buildResolution(res);
    });
    buildIconJson();
    copyFonts();
}


function startWatch () {
    console.log('Watcher Start');
    console.log(' ');

    chokidar.watch(path.join(basePath,'../../', '/src')).on('all', buildAll);
}

function buildResolution ( resolution ) {
    var fileName = path.join(themePath,'src', resolution + '.scss'),
        dest = path.join(themePath, resolution + '.css');

    console.log('from: ' + fileName);
    console.log('to: ' + dest);

    sass.render({
        file: fileName
    }, function ( error, data ) {
        if ( error ) {
            console.log(error);
            return;
        }
        fs.writeFile(dest, data.css, function ( err ) {
            if ( err ) {
                console.log(err);
            } else {
                console.log('Build ' + resolution + '.css');
            }
        })
    });
}

function buildIconJson ( command ) {
    var iconsFileName =  path.join(basePath,'src', 'icons' + '.scss');

    fs.readFile(iconsFileName, 'utf8', function ( error, data ) {
        var iconsArray = [],
            moduleTxt = 'module.exports = {\n';

        if ( error ) {
            console.log(error)
        } else {
            data = data.split('@include icon(');
            data = data.splice(1);
            data.forEach(function ( icon ) {
                icon = icon.replace(/[',);]/gi, '').split(' ');
                iconsArray.push({
                    className:'theme-icon-' + icon[0],
                    symbol: icon[1]
                });
                if ( command === 'icons-module' ) {
                    moduleTxt += '    ' + icon[0].toUpperCase().replace(/-/g, '_') + ': ' + '\'theme-icon theme-icon-' + icon[0] + '\',\n'
                }
            });

            moduleTxt += '    prefix: \'theme-icon\' \n};';

            iconsArray = JSON.stringify(iconsArray);

            if ( command === 'icons-module' ) {
                try {
                    fs.writeFileSync(path.join(themePath, 'icons.js'), moduleTxt);
                } catch ( e ) {
                    console.log('error write module file');
                }
            }
            fs.writeFile(path.join(themePath, 'icons.json'), iconsArray, null, function ( error ) {
                if ( error ) {
                    console.log(error);
                } else {
                    console.log('- Copy index page to -> ' + path.join(themePath, 'index.html'));
                    fs.createReadStream(path.join(basePath, 'index.html')).pipe(fs.createWriteStream(path.join(themePath, 'index.html')))
                }
            });

        }
    });

}



function convertColor ( color ) {
    return 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + (color.o || 1) + ')';
}

function makeVar ( name, value ) {
    return '$' + name + ': ' + value + ';\n';
}

function writeScss ( data, dst ) {
    fs.writeFileSync(path.join(themePath,'src', dst),data, 'utf8');
}


function buildVarsSCSS () {
    var json = require(path.join(themePath,'src', 'vars.json')),
        outputScss = '';

    Object.keys(json).forEach(function ( variable ) {
        switch ( json[variable].type ) {
            case TYPE_NUMBER:
                outputScss += makeVar(variable, json[variable].value);
                break;
            case TYPE_STRING:
                outputScss += makeVar(variable, "'" + json[variable].value + "'");
                break;
            case TYPE_COLOR:
                outputScss += makeVar(variable, convertColor(json[variable]));
                break;
        }
    });
    writeScss(outputScss,'vars.scss');
}

function copyFonts() {
    var fontNames = fs.readdirSync(path.join(basePath, 'fonts'));

    fontNames.forEach(function ( font ) {
        fs.createReadStream(path.join(basePath, 'fonts', font)).pipe(fs.createWriteStream(path.join(themePath, 'fonts', font)));
    });
}
