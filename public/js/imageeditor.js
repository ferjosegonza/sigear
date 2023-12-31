//var fileInput = document.querySelector("#upload");

// enabling drawing on the blank canvas
//drawOnImage();

/*fileInput.addEventListener("change", async (e) => {
  var [file] = fileInput.files;

  // displaying the uploaded image
  var image = document.createElement("img");
  image.src = await fileToDataUri(file);

  // enbaling the brush after after the image
  // has been uploaded
  image.addEventListener("load", () => {
    drawOnImage(image);
  });

  return false;
});

function fileToDataUri(field) {
  return new Promise((resolve) => {
    var reader = new FileReader();

    reader.addEventListener("load", () => {
      resolve(reader.result);
    });

    reader.readAsDataURL(field);
  });
}*/

var sizeElement = document.querySelector("#sizeRange");
var size = sizeElement.value;
sizeElement.oninput = (e) => {
  size = e.target.value;
};

var colorElement = document.getElementsByName("colorRadio");
var color;
colorElement.forEach((c) => {
  if (c.checked) color = c.value;
});

colorElement.forEach((c) => {
  c.onclick = () => {
    color = c.value;
  };
});

function getAspectRatio(image) {
  const imgwidth = image.naturalWidth || image.width;
  const imgheight = image.naturalHeight || image.height;
  return  imgheight / imgwidth;
}

function drawOnImage(image = null) {
  var canvasElement = document.getElementById("canvas");

  var imgEditor = document.getElementById("imgEditor");
  var imgEditorRect = imgEditor.getBoundingClientRect();
  
  var context = canvasElement.getContext("2d");

  // if an image is present,
  // the image passed as a parameter is drawn in the canvas
  if (image) {
    
   
    var imgRatio = getAspectRatio(image);//image.height / image.width;
    var newHeight = imgEditorRect.width * imgRatio

    image.width = imgEditorRect.width;
    image.height = newHeight;

    var imageWidth = image.width;
    var imageHeight = image.height;
    // rescaling the canvas element
    //canvasElement.style.width = imageWidth
    //canvasElement.style.height = imageHeight
    canvasElement.width = imgEditorRect.width;
    canvasElement.height = imageHeight;

    context.drawImage(image, 0, 0, imageWidth, imageHeight);
  }

  var isDrawing;

  canvasElement.onmousedown = (e) => {
    isDrawing = true;
    context.beginPath();
    context.lineWidth = size;
    context.strokeStyle = color;
    //context.lineJoin = "round";
    //context.lineCap = "round";
    
    //OPACITY PARA TACHAR O SUBRALLAR
    if(color == 'yellow'){
        context.globalAlpha = 0.02
    } else {
        context.globalAlpha = 1
    }
    var rect = e.target.getBoundingClientRect();
    var x = e.clientX - rect.left; //x position within the element.
    var y = e.clientY - rect.top;  //y position within the element.    
    context.moveTo(x, y);
  };

  canvasElement.onmousemove = (e) => {
    if (isDrawing) {
        var rect = e.target.getBoundingClientRect();
        var x = e.clientX - rect.left; //x position within the element.
        var y = e.clientY - rect.top;  //y position within the element.      
        context.lineTo(x, y );
        context.stroke();
    }
  };

  canvasElement.onmouseup = function () {
    isDrawing = false;
    context.closePath();
  };
}
