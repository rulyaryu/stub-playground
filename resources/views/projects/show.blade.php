<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
    
        img {
            max-width: 100%;
        }

        .flex {
            display: flex;
            flex-wrap: wrap;
        }
    </style>

</head>
<body>
    

    <h1>
        {{$project->title}}
    </h1>
    <div>
        {{$project->description}}
    </div>

    <div class="flex" style="width:100%;">
        @foreach($project->getMedia('test2') as $media)   
        <img width="100%" src="{{$media->getUrl()}}" alt="">
        @endforeach
    </div>

    <form method="POST" 
    enctype="multipart/form-data" action={{"/project/edit/" . $project->id}}>

                @csrf
        <input type="file" name="image" id="">



        <input type="submit">
    </form>

</body>
</html>