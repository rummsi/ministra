/**
 * This file used only in meta package.
 */

'use strict';


var execSync = require('child_process').execSync,
    path = require('path'),
    fs   = require('fs');


function resolveModulesDir ( dependencies ) {
    // save level as metapackage directory
    var modulesDir = path.join(__dirname, '..');

    try {
        dependencies.forEach(function ( dependency ) {
            console.log('try to resolve ' + dependency + ' on new way');
            fs.statSync(path.join(modulesDir, dependency, 'package.json'));
        });

        return modulesDir;
    } catch ( error ) {
        console.log(error);
        console.log('not founded on the same level as metapackage');
    }

    // in the metapackage node_modules
    modulesDir = path.join(__dirname, 'node_modules');

    try {
        dependencies.forEach(function ( dependency ) {
            console.log('try to resolve ' + dependency + ' on old way');
            fs.statSync(path.join(modulesDir, dependency, 'package.json'));
        });

        return modulesDir;
    } catch ( error ) {
        console.log(error);
        console.log('not founded on the node_modules in metapackage');
    }

    throw new Error(__filename + ': can\'t resolve modules directory');
}


/* eslint max-nested-callbacks: 0 */
fs.readFile(path.join(__dirname, 'package.json'), function ( error, metaPackageData ) {
    var dependencies, modulesDir;

    if ( error ) {
        throw error;
    }

    if ( !process.env.NPM_CONFIG_USERCONFIG ) {
        throw new Error(__filename + ': no environment config provided');
    }

    metaPackageData = JSON.parse(metaPackageData);

    dependencies = Object.keys(metaPackageData.dependencies);
    modulesDir = resolveModulesDir(dependencies);

    dependencies.forEach(function ( dependency ) {
        var packageData = JSON.parse(fs.readFileSync(path.join(modulesDir, dependency, 'package.json'))),

            clearPackageData = {
                // package.json contents without auto-generated properties
            };

        Object.keys(packageData).forEach(function ( key ) {
            if ( key.substring(0, 1) !== '_' && key !== 'dist' ) {
                clearPackageData[key] = packageData[key];
            }
        });

        console.log('modifiyng package.json for ' + dependency);
        fs.writeFileSync(path.join(modulesDir, dependency, 'package.json'), JSON.stringify(clearPackageData));

        console.log('publish package ' + dependency);
        execSync('npm publish', {
            cwd: path.join(modulesDir, dependency),
            env: {
                NPM_CONFIG_USERCONFIG: process.env.NPM_CONFIG_USERCONFIG
            }
        }, function ( error, stdout ) {
            console.log(stdout);

            if ( error ) {
                console.log(error);
            } else {
                console.log(dependency + ' published successfully');
            }
        });
    });
});
