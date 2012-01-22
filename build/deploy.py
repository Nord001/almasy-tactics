#!/usr/bin/env python

import os
import datetime
import shutil
import zipfile

BACKUP_FOLDER = '/root/almasy_bak'
WEBSITE_ROOT = 'html'
TMP_NEW_FOLDER = 'almasy-new'

USE_SYSTEM = 1

def Unzip():
    files = os.listdir('.')
    zips = [file for file in files if '.zip' in file]
    zips.sort()
    file = zips[-1]

    newFolderExists = True
    try:
        os.stat(TMP_NEW_FOLDER)
    except OSError, e:
        newFolderExists = False

    if newFolderExists:
        print 'Folder already exists, stopping.'
        return

    print 'Unzipping %s to %s...' % (file, TMP_NEW_FOLDER)

    if USE_SYSTEM:
        returnValue = os.system('unzip -d %s %s' % (TMP_NEW_FOLDER, file))
        if returnValue == 0:
            print '>>>>> SUCCESS'
        else:
            print '>>>>> FAILED'
    else:
        try:
            zipFile = zipfile.ZipFile(file)
            zipFile.extractall(TMP_NEW_FOLDER)
            print '>>>>> SUCCESS'
        except zipfile.BadZipfile, e:
            print '>>>>> FAILED (%s)' % e


def TurnOnMaintenance():
    print '>>>>> Turning on maintenance...'
    try:
        open(os.path.join(WEBSITE_ROOT, 'app/webroot/MAINTENANCE_ON'), 'a').close()
        print '>>>>> SUCCESS'
    except IOError, e:
        print '>>>>> FAILED (%s)' % e


def MoveForums():
    print '>>>>> Moving forums to new site...'
    try:
        os.rename(os.path.join(WEBSITE_ROOT, 'forums'), os.path.join(TMP_NEW_FOLDER, 'forums'))
        print '>>>>> SUCCESS'
    except OSError, e:
        print '>>>>> FAILED (%s)' % e


def Backup():
    print '>>>>> Moving old site to backup...'
    try:
        os.rename(WEBSITE_ROOT, '%s/almasy_%s' % (BACKUP_FOLDER, datetime.date.today()))
        print '>>>>> SUCCESS'
    except OSError, e:
        print '>>>>> FAILED (%s)' % e


def DeployNewSite():
    print '>>>>> Deploying new site...'
    try:
        os.rename(TMP_NEW_FOLDER, WEBSITE_ROOT)
        returnValue = os.system('chown -R lighttpd %s' % WEBSITE_ROOT)
        if returnValue == 0:
            print '>>>>> SUCCESS'
        else:
            print '>>>>> FAILED'
        print '>>>>> SUCCESS'
    except OSError, e:
        print '>>>>> FAILED (%s)' % e


def DoEverything():
    print 'DOING EVERYTHING!!!'
    Unzip()
    TurnOnMaintenance()
    MoveForums()
    Backup()
    DeployNewSite()

commands = [
    ('Unzip new package', Unzip),
    ('Turn on maintenance', TurnOnMaintenance),
    ('Move forums', MoveForums),
    ('Move old site to backup', Backup),
    ('Deploy new site', DeployNewSite),
    ('ALL OF THEM!', DoEverything)
]


def GetCommand():
    while True:
        try:
            command = raw_input('Command: ')
            command = int(command)
            return command
        except ValueError, e:
            print 'Error.'


def main():
    print 'Almasy Deploy Script v0.5'
    while True:
        print '----------------------------------'
        i = 1
        for command in commands:
            print '%d) %s' % (i, command[0])
            i += 1
        print '%d) Quit' % i
        print '----------------------------------'

        while True:
            command = GetCommand()
            if command >= 1 and command <= i:
                break

        if command == i:
            return

        commands[command - 1][1]()


if __name__ == '__main__':
    main()
