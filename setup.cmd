@echo off
TITLE InstagramPPUpdater Code by An!l [Setup]
cd /d %~dp0

if exist bin\php\php.exe (
    set PHPRC=""
    set PHP_BINARY="bin\php\php.exe"
) else (
    set PHP_BINARY="php"
)

if exist src\Artemis\setup-windows.php (
    set RUNNER_FILE="src\Artemis\setup-windows.php"
) else (
    echo src\Artemis\setup-windows.php not found.
    pause
    exit 1
)

%PHP_BINARY% -c bin\php %RUNNER_FILE% %* || pause