const fs = require('fs');

async function checkComposerExecutedInFolder(folder) {
    const vendorFolder = folder + "/vendor"

    try {
        await fs.promises.access(vendorFolder);
    } catch (error) {
        throw new Error(`${vendorFolder} does not exist. 'composer install' needs to be executed in ${folder}`);
    }
}

function ignoredComposerFilesInFolder(folder) {
    return [
        '!' + folder + '/vendor/**/Test',
        '!' + folder + '/vendor/**/Test/**',
        '!' + folder + '/vendor/**/Tests',
        '!' + folder + '/vendor/**/Tests/**',
        '!' + folder + '/vendor/**/doc',
        '!' + folder + '/vendor/**/doc/**',
        '!' + folder + '/vendor/**/docs',
        '!' + folder + '/vendor/**/docs/**',
        '!' + folder + '/vendor/**/composer.*',
        '!' + folder + '/vendor/**/build.php',
        '!' + folder + '/vendor/**/phpunit.*',
        '!' + folder + '/vendor/**/phpunit.*',
        '!' + folder + '/composer.*',
    ];
}

module.exports = {
    checkComposerExecutedInFolder,
    ignoredComposerFilesInFolder,
}
