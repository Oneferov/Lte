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
              tab = 9,
              keyFirstCommand = 'command_',
              currentNumKey = 'currentNum',
              maxNumKey = 'maxNum',
              keyCurrentFolder = 'currentFolder',
              keyCurrentComand = 'currentComand',
              keyLevelFolder = 'levelFolder',
              info = $('.header__info'),
              consoleStr= $('#console')

        $(function() {
            changeLabel(sessionStorage.getItem(keyCurrentFolder) ?? changeOutputFolder())

            $('#console').on('click', function () {
                info.slideDown(500)
            })

            $('#console').keydown(function(e) {
                if(e.keyCode === codeEnter) {

                    if ($(this).val() == 'clear') {
                        $('.header__info').empty()
                        $(this).val('')
                    } else {
                        if ($(this).val() == 'cd ..') {
                            sessionStorage.setItem(keyLevelFolder, +sessionStorage.getItem(keyLevelFolder) + 1)
                            sessionStorage.setItem(keyCurrentComand, sessionStorage.getItem(keyCurrentComand) ? sessionStorage.getItem(keyCurrentComand) + 'cd .. && ' : 'cd .. && ')
                            changeOutputFolder()
                        }

                        setLocalStorage($(this).val())
                        sendCommand(sessionStorage.getItem(keyCurrentComand) ? sessionStorage.getItem(keyCurrentComand) + $(this).val() : $(this).val())
                    }
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

                if ($(this).val() != '' && e.keyCode === tab) {
                    e.preventDefault()
                    getLs(sessionStorage.getItem(keyCurrentFolder), 'tab') 
                }
            });
        });

        function getLs(path, value = null) {
            $.ajax({
                url: '{{ route('console.ls') }}',
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    path
                },
                success:function(response){
                    if (value) {
                        changeConsoleWithTab(response)
                    } else {
                        changeInfoWithLs(response)
                    }
                },
                error: function (error) {

                }
            });
        }

        function changeConsoleWithTab(response) {
            let arrValue = consoleStr.val().split(' ')
            let value = arrValue[arrValue.length - 1]
            response.folders.concat(response.files).forEach(element => {
                if (element.startsWith(value)) {
                    arrValue[arrValue.length - 1] = element
                    consoleStr.val(arrValue.join(' '))
                }
            });
        }

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
            $.ajax({
                url: '{{ route('console.execute') }}',
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'path': sessionStorage.getItem(keyCurrentFolder),
                    command
                },
                success:function(response){
                    if (command.split(' ')[command.split(' ').length - 1]=== 'ls') {
                        getLs(sessionStorage.getItem(keyCurrentFolder))
                    } else if (consoleStr.val().includes('cd ') && !consoleStr.val().includes('..')) {
                        if (response.error) {
                            info.css('color', 'red').append($(`<div>${response.message}</div>`))
                        } else {
                            let newpath = consoleStr.val().split(' ')[1]
                            if (newpath.split('/').length > 1) {
                                sessionStorage.setItem(keyLevelFolder, +sessionStorage.getItem(keyLevelFolder) - newpath.split('/').length)
                            } else {
                                sessionStorage.setItem(keyLevelFolder, +sessionStorage.getItem(keyLevelFolder) - 1)
                            }
                            changeLabel(sessionStorage.getItem(keyCurrentFolder) + '/' + newpath)
                            sessionStorage.setItem(keyCurrentFolder, sessionStorage.getItem(keyCurrentFolder) + '/' + newpath)
                            sessionStorage.setItem(keyCurrentComand, sessionStorage.getItem(keyCurrentComand) ? sessionStorage.getItem(keyCurrentComand) : `cd ${newpath} && `)

                            setLocalStorage(consoleStr.val())
                        }
                    } else {
                        info.css('color', 'greenyellow').append($(`<div>${response}</div>`))
                    }

                    consoleStr.val('')
                },
                error: function (error) {
                    info.css('color', 'red').append($(`<div>${error}</div>`))
                }
            });
        }

        function changeLabel(value) {
            $('.header__label').text(value + '/')
        }

        function changeInfoWithLs(response) {
            let root = $('<div></div>').css({
                'display': 'flex',
                'flexWrap': 'nowrap',
                'gap': '40px'
            }),
                columns = [],
                column = 1,
                length = response.folders.length + response.files.length,
                lengthFolders = response.folders.length

            for (let i = 1; i<=4; i++) {
                columns.push($(`<div class="column" style="max-width:250px"></div>`));
            }

            columns = addItemsToColumns(response.folders, columns, '#2f98d4', length)
            columns = addItemsToColumns(response.files, columns, '#6f6f21', length)

            columns.forEach((value) => {
                root.append(value)
            })
            info.append(root)
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

        function addItemsToColumns(arr, columns, color, length) {
            arr.forEach((value, key) => {
                let resultKey = (color == '#6f6f21') ? key + length - arr.length : key
                if (resultKey >= length/4*3) {
                    column = 3
                } else if (resultKey >= length/4*2) {
                    column = 2
                } else if (resultKey >= length/4) {
                    column = 1
                } else {
                    column = 0
                }
                let result = (color == '#6f6f21') ? `-${value}` : `+${value}`
                columns[column].append($(`<div>${result}</div>`).css({
                    'color': color,
                    'overflow': 'hidden',
                    'whiteSpace': 'nowrap'
                }))
            });
            return columns
        }

    </script>
@stop