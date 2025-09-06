#Municipio - Image Convert
Municipio has a unique way of handling images rendered on the page. Instead of relying on WordPress standard functionality for selecting the most apropriate image size to display, the platform will create the images on demand. This is done, by requesting a image by pixel size instead of a name. 

This code for example, will create a thumbnail of inserted image id with the excact dimension of 100x100 pixels. Any of these values can be swapped with false, to effectivly preserve the original image dimensions, keeping the ratio. 

```
wp_get_attachment_img_src($id, [100,100])
````

## The image contract
The image contract is not strictly required when utilizing the image conversion feature. But it is relevant to know, that the image contract feature will request additional sizes of the image for the purpose to deliver apropriate variants of the image to smaller screen devices like phones etc. The image contract will also create a LQIP (Low Quality Image Placehoder), to display before any other image has been able to load. This is made due to some slowness in most browsers to determine the most appropriate image size to use. Bear in mind that we use container querys on the frontendand not simply a srcset.

## Strategies
The advanced system for image resizing and optiomization has a number of strategies implemented, these are: 

- Runtime (default)
  This strategy is the default, and simply creates the images while the page is loading. This is suitable for systems handling low traffic sites, possibly without more advanced access to crontab systems.
- Background
  This method will identify what images that we need to create when a page is first loaded. In runtime, we will collect the data about image id's and requested their respective sizes. These image sizes are then processed in the background by a cronjob. 
- Mixed
  This is a mix of the above strategies. For public users (users without editing permissions) the background strategy will apply. But for logged in users, we will first check that if it is the author of the page that is visiting the page less than an hour after updating the page. If this can be determined; we are quite shure that the editor want to se the exact future results. In this case, the priority is to display a correct page with resized images. 
- CLI
  This is just a tool to manage the backgrounds methods queue system with the ability to process the queue manually, investigate its content. This is designed as a simple CRUD interface. 


## Overall performance
The system is designed to try to convert all images, somethimes this will fail. In thse cases the image conversion will be logged, and the image id will be suspended from further processing. This suspension will live, for 24 hours util it's allowed to be processed again. 

We use the wp cache sisyetm to keep track of all of this, making this a requirement to get grate performance out of the solution (it will work without it, but we REALLY recommend to use redis or memcached). 