<!DOCTYPE html>
<html>
<head>
    <title>Grid & InteractJS</title>

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <?php echo asset('css/lib/bootstrap.min.css', true); ?>
    <?php echo asset('css/lib/jquery.dataTables.min.css', true) ?>
    
    <style>
        .rowbar {
            background-color: grey;
            padding: 10px 0 10px 10px;
            /*width: 150px;*/
            cursor: pointer;
        }

        .btn_resize {
            border-radius: 50%;
            border: 1px solid black;
            background-color: white;
            float:right;
            margin-right: -5px;
        }

        .selected {
            background-color: red;
        }
    </style>
</head>
<body>
    <h1>Grid & InteractJS</h1>

    <table id="datatable">
        <thead>
            <th></th>
            <?php foreach ($timeListHeader as $time => $value): ?>
            <th><?php echo $time; ?></th>
            <?php endforeach ?>
        </thead>
        <tbody>
            <?php $this->load->view('grid/partials/_list'); ?>
        </tbody>
    </table>
</body>
<script src="<?php echo asset('js/lib/jquery-3.3.1.min.js'); ?>"></script>
<script src="<?php echo asset('js/lib/bootstrap.min.js'); ?>"></script>
<script src="<?php echo asset('js/lib/jquery.dataTables.min.js'); ?>"></script>
<script src="<?php echo asset('js/lib/interact.js'); ?>"></script>
<script>
    // $('#datatable').DataTable({
    //     scrollY:        "500px",
    //     scrollCollapse: true,
    //     paging: false
    // });

    interact('.rowbar')
        .draggable({
            manualStart: true,
            axis: 'x',
            restrict: {
                restriction: $('#datatable')[0],
                endOnly: true,
                elementRect: { top: 0, left: 0, bottom: 1, right: 1 }
            },
            autoScroll: true,
            onstart: dragstart,
            onmove: dragmove,
            onend: dragend
        })
        .resizable({
            manualStart: true,
            // resize from only right edges and corners
            edges: { left: false, right: true, bottom: false, top: false },
            // keep the edges inside the parent
            restrictEdges: {
                outer: $('#datatable')[0],
                endOnly: true,
            },
            // minimum size
            // restrictSize: {
            //     min: { width: 150 },
            // },
            inertia: true,
        })
        .on('resizemove', function (event) {
            var target = $(event.target).closest('.rowbar')[0];
            var x = (parseFloat(target.getAttribute('data-x')) || 0);

            // update the element's style
            target.style.width = event.rect.width + 'px';

            // keep the dragged position in the data-x/data-y attributes
            //var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
            //var y = 0;
            //var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

            // translate the element
            target.style.webkitTransform = target.style.transform = 'scaleX(' + event.rect.width + ')';

            // update the posiion attributes
            //target.setAttribute('data-x', x);
        })
        .on('tap', function (event) {
            var currentTarget = event.currentTarget;
            var classList = currentTarget.classList;
            if (!classList.contains('btn_resize')) {
                if (classList.contains('selected')) {
                    currentTarget.classList.remove('selected');
                } else {
                    currentTarget.classList.add('selected');
                }
            }
        });

    // start drag
    interact('.rowbar').on('down', function (event) {
        event.interaction.start({name: 'drag'}, event.interactable, event.currentTarget);
    });

    // start resize
    interact('.btn_resize').on('down', function (event) {
        event.interaction.start({
                name: 'resize',
                edges: { left: false, right: true, bottom: false, top: false }
            },
            interact('.rowbar'), // target Interactable
            event.currentTarget  // target Element
        );
    })

    function dragstart(event) {
        console.log('dragstart');
    }

    function dragmove(event) {
        var target = event.target;
        // keep the dragged position in the data-x/data-y attributes
        var x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
        var y = 0;
        //var y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

        // translate the element
        target.style.webkitTransform = target.style.transform = 'translate(' + x + 'px, ' + y + 'px)';

        // update the posiion attributes
        target.setAttribute('data-x', x);
        target.setAttribute('data-y', y);
    }

    function dragend(event) {
        console.log('dragend');
    }
</script>
</html>
