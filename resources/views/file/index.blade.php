@extends('layouts.main')

@section('title', 'Files')

@section('content_header')
    <h1>Файловый менеджер</h1>
@stop

@section('content')
    <div class="flexable file__content">
        <div class="file__tree">
            @foreach ($collection as $key => $item)
            <div class="itemable">
                @if (is_array($item))
                    <div class="rootable clickable" data-path="{{'/'.$key}}"><span>+</span>{{$key}}</div>
                    @include('components.item-tree', ['model' => my_sort($item), 'path' => "/$key/"])
                @else
                    <div class="greenable openable" data-path={{'/'.$item}}>{{$item}}</div>
                @endif
            </div>
            @endforeach
        </div>

        <div>
            <form id="send_content">
                <label for="content" class="file__label">./</label>
                <x-adminlte-textarea name="content" id="content"/>
                <x-adminlte-button onclick="event.preventDefault();sendContent()" class="button_content save_content" style="display:none" type="submit" label="Сохранить" theme="success" icon="fas fa-lg fa-save"/>
                <x-adminlte-button onclick="event.preventDefault();cancelContent()" label="Отмена" theme="dark" class="button_content cancel" style="display:none" icon="fas fa-adjust"/>
            </form>
            <div class="alert alert-block" id="alert" style="display:none">
                <button type="button" class="close" data-dismiss="alert">×</button>	
                <strong>Успешно сохранено</strong>
            </div>
        </div>
    </div>

    <script>
        const area = $('#content'),
              btns = $('.button_content'),
              label = $("label[for='content']")

        $(function() {
            $('.clickable').on('click', function() {
                $(this).siblings('.itemable').slideToggle(700).css('marginLeft', '10px')
                toggleSpan(this)
            })

            $('.openable').on('click', function() {
                let path = $(this).attr('data-path')
                showContent(path)
            })
        });

        function toggleSpan(node) {
            let text = $(node).children()
            if (text.text() == '+') {
                text.text('-')
            } else {
                text.text('+')
            }
        }
        
        function showContent(path) {
            $.ajax({
                url: '{{ route('file.show') }}',
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    path
                },
                success:function(response){
                    area.val(response)
                        .attr('data-path', path)
                    label.text('.'+path)
                    btns.slideDown(1000);
                },
                error: function (error) {
                    area.val(error);
                    btns.slideUp(1000)
                    showAlert('alert-danger', 'Произошла ошибка, повторите позднее')
                }
            });
        }

        function sendContent() {
            let path = area.attr('data-path'),
                content = area.val()
            $.ajax({
                url: '{{ route('file.save') }}',
                type:"POST",
                data:{
                    "_token": "{{ csrf_token() }}",
                    path,
                    content
                },
                success:function(response){
                    area.val('').attr('data-path', '')
                    label.text('.')
                    btns.slideUp(1000)
                    showAlert('alert-success', 'Успешно сохранено')
                },
                error: function (error) {
                    btns.slideUp(1000)
                    showAlert('alert-danger', 'Произошла ошибка, повторите позднее')
                }
            });
        }

        function cancelContent() {
            area.val('').attr('data-path', '')
            label.text('.')
            btns.slideUp(1000)
        }

        function showAlert(className, message) {
            let alert = $('#alert')
            alert.addClass(className).slideDown(1000);
            alert.children("strong").text(message);
            setTimeout(() => {
                alert.slideUp(1000)
            }, 3000);
        }
    </script>
@stop
