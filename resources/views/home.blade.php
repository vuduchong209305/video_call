<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.4.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">

            <form action="#" method="get" id="form_call">
                <div class="form-group">
                    <label>Người gọi</label>
                    <input type="text" class="form-control" placeholder="ID người gọi" id="from_user" required="">
                </div>
                <div class="form-group">
                    <label>Người nhận</label>
                    <input type="text" class="form-control" placeholder="ID người nhận" id="to_user" required="">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#form_call').submit(function(e){
                e.preventDefault()

                const from_user = $('#from_user').val()
                const to_user = $('#to_user').val()

                window.open(`http://videocall.test/video_call?userId=${from_user}&fromInternal=true&call_id=${to_user}`,'popup','width=1000,height=800');
            })
        })
    </script>
</body>
</html>