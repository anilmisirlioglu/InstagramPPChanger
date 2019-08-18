    [CmdletBinding(PositionalBinding=$false)]
    param (
        [string]$php = "",
        [switch]$Loop = $false,
        [string]$file = "",
        [string][Parameter(ValueFromRemainingArguments)]$extraKodPortaliArgs
    )

    if($php -ne ""){
        $binary = $php
    }elseif(Test-Path "bin\php\php.exe"){
        $env:PHPRC = ""
        $binary = "bin\php\php.exe"
    }else{
        $binary = "php"
    }

    if($file -eq ""){
        if(Test-Path "src/Artemis/run.php"){
            $file = "src/Artemis/run.php"
        }else{
            echo "src\Artemis\run.php not found."
            pause
            exit 1
        }
    }

    function StartSystem{
        $command = "powershell -NoProfile " + $binary + " " + $file + " " + $extraKodPortaliArgs
        iex $command
    }

    $loops = 0

    StartSystem

    while($Loop){
        if($loops -ne 0){
            echo ("Restarted " + $loops + " times")
        }
        $loops++
        echo "To escape the loop, press CTRL+C now. Otherwise, wait 5 seconds for the server to restart."
        echo ""
        Start-Sleep 5
        StartServer
    }