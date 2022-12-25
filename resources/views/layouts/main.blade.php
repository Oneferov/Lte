@extends('adminlte::page')

@section('content_top_nav_right')
    <div class="header__block">
        <div class="header__info"></div>
        <div class="header__console">
            <label class="header__label" for="console">public</label>
            <input name="console" id="console" type="text">
        </div>
    </div>

    <script>
        const firstNum = 1,
              codeEnter = 13,
              codeUp = 38,
              codeDown = 40,
              keyFirstCommand = 'command_',
              currentNumKey = 'currentNum',
              maxNumKey = 'maxNum',
              keyCurrentFolder = 'currentFolder',
              keyCurrentComand = 'currentComand',
              keyLevelFolder = 'levelFolder'

        $(function() {
            changeLabel(sessionStorage.getItem(keyCurrentFolder) ?? changeOutputFolder())

            $('#console').keydown(function(e) {
                if(e.keyCode === codeEnter) {

                    if ($(this).val() == 'clear') {
                        $('.header__info').empty()
                    }

                    if ($(this).val() == 'cd ..') {
                        sessionStorage.setItem(keyLevelFolder, +sessionStorage.getItem(keyLevelFolder) + 1)
                        sessionStorage.setItem(keyCurrentComand, sessionStorage.getItem(keyCurrentComand) ? sessionStorage.getItem(keyCurrentComand) + 'cd .. && ' : 'cd .. && ')
                        changeOutputFolder()
                    }

                    if ($(this).val().includes('cd ') && !$(this).val().includes('..')) {
                        let newpath = $(this).val().split(' ')[1]
                        if (newpath.split('/').length > 1) {
                            sessionStorage.setItem(keyLevelFolder, +sessionStorage.getItem(keyLevelFolder) - newpath.split('/').length)
                        } else {
                            sessionStorage.setItem(keyLevelFolder, +sessionStorage.getItem(keyLevelFolder) - 1)
                        }
                        changeLabel(sessionStorage.getItem(keyCurrentFolder) + '/' + newpath)
                        sessionStorage.setItem(keyCurrentFolder, sessionStorage.getItem(keyCurrentFolder) + '/' + newpath)
                        sessionStorage.setItem(keyCurrentComand, sessionStorage.getItem(keyCurrentComand) ? sessionStorage.getItem(keyCurrentComand) + `cd ${newpath} && ` : `cd ${newpath} && `)
                    }

                    setLocalStorage($(this).val())
                    sendCommand(sessionStorage.getItem(keyCurrentComand) ? sessionStorage.getItem(keyCurrentComand) + $(this).val() : $(this).val())
                    $(this).val('')
                }

                if (localStorage.getItem(maxNumKey)) {
                    if (e.keyCode === codeUp) {
                        if (localStorage.getItem(currentNumKey) == localStorage.getItem(maxNumKey)) {
                            $(this).val(localStorage.getItem(keyFirstCommand + localStorage.getItem(maxNumKey)))
                            localStorage.setItem(currentNumKey, +localStorage.getItem(currentNumKey) - 1)
                        } else if (localStorage.getItem(currentNumKey) != 0) {
                            $(this).val(localStorage.getItem(keyFirstCommand +localStorage.getItem(currentNumKey)))
                            localStorage.setItem(currentNumKey, +localStorage.getItem(currentNumKey) - 1)
                        } 
                    } 
                    if (e.keyCode === codeDown) {
                        if  (localStorage.getItem(currentNumKey) != localStorage.getItem(maxNumKey)) {
                            localStorage.setItem(currentNumKey, +localStorage.getItem(currentNumKey) + 1)
                            $(this).val(localStorage.getItem(keyFirstCommand + localStorage.getItem(currentNumKey)))
                        } else {
                            $(this).val(localStorage.getItem(keyFirstCommand + (+localStorage.getItem(maxNumKey))))
                        }
                    }
                }
            });
        });

        function setLocalStorage(command) {
            let nextKey = firstNum,
                nextMaxKey = localStorage.getItem(maxNumKey)

            if (nextMaxKey) {
                nextKey = +nextMaxKey + 1
            } 
            localStorage.setItem(keyFirstCommand + nextKey, command)
            localStorage.setItem(maxNumKey, nextKey)
            localStorage.setItem(currentNumKey, nextKey)
        }

        function sendCommand(command) {
            let info = $('.header__info')
            $.ajax({
                url: '{{ route('console.execute') }}',
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    command
                },
                success:function(response){
                    if (!response) {
                        response = 'Не является папкой'
                    } 
                    info.css('color', 'greenyellow').append($(`<div>${response}</div>`))
                },
                error: function (error) {
                    info.css('color', 'red').append($(`<div>${error}</div>`))
                }
            });
        }

        function changeLabel(value) {
            $('.header__label').text(value + '/')
        }

        function changeOutputFolder() {
            $.ajax({
                url: '{{ route('console.execute') }}',
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    "command": "pwd"
                },
                success:function(response){
                    let value = getRealPath(response, +sessionStorage.getItem(keyLevelFolder))
                    sessionStorage.setItem(keyCurrentFolder, value)
                    changeLabel(value)
                },
                error: function (error) {
                    
                }
            });
        }

        function getRealPath(str, count) {
            if (count) {
                let value = str.substr(0, str.lastIndexOf('/'))
                return getRealPath(value, --count)
            } else {
                return str
            }
        }

    </script>
@stop


<style>
    .header__block {
        width: 80rem;
    }
    .header__console {
        color: greenyellow;
        position: relative;
    }

    .header__label {
        position: absolute;
        left: .5rem;
        bottom: 2.3rem;
    }

    .header__info {
        height: 20rem;
        background: black;
    }

    #console {
        min-width: 100%;
        background: black;
        color: greenyellow;
    }
</style>