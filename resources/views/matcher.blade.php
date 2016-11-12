<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Match closer contacts</title>
        
        <script>
            window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
            ]); ?>
        </script>
        
    </head>
    <body>
        
        <div id="app">
        
            <contacts></contacts>
            
        </div>
        
        
        
        
        <script src="/js/app.js"></script>
    
    </body>
</html>