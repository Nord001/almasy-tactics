#!/usr/bin/python2.6

import os
import os.path
import re

def main ():
    codeTypes = ['php', 'ctp', 'js', 'py']
    codeFileRegex = re.compile('(' + "|".join(codeTypes) + ')$')
    excludedDirs = ['third_party', '.svn', 'tmp', 'tests', 'vendors']

    fileCount = 0
    codeFilesCount = 0
    codeLinesCount = 0
    codeTypesDict = {}

    for root, dirs, files in os.walk('../root/app'):
        for excludeDir in excludedDirs:
            if excludeDir in dirs:
                dirs.remove(excludeDir)

        for file in files:
            fileCount += 1
            if codeFileRegex.search(file):
                if 'test' in file:
                    continue
                print file
                codeFilesCount += 1
                count = sum([1 for line in open(os.path.join(root, file))])
                codeLinesCount += count
                for codeType in codeTypes:
                    if re.search(codeType + '$', file):
                        if codeType in codeTypesDict:
                            codeTypesDict[codeType] += count
                        else:
                            codeTypesDict[codeType] = count

    print 'Files Scanned: '.ljust(15), str(fileCount).rjust(5)
    print 'Code Files: '.ljust(15), str(codeFilesCount).rjust(5)
    print 'Code Type Breakdown:'
    for codeType in codeTypesDict:
        print codeType.upper().ljust(15), str(codeTypesDict[codeType]).rjust(5)
    print 'Lines of Code: '.ljust(15), str(codeLinesCount).rjust(5)

if __name__ == '__main__':
    main()
