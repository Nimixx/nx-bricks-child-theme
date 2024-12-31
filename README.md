# Bricks Child Theme

## Asset Manager

The Asset Manager is a core component that handles all asset loading and management for the child theme. It uses a singleton pattern to ensure only one instance is running and provides automatic asset enqueueing based on the folder structure.

### Folder Structure

wp-content/themes/bricks-child/
├── core/
│ └── AssetManager.php # Core asset management functionality
├── backend/
│ └── assets/
│ ├── css/ # Backend CSS files
│ ├── scripts/ # Backend JavaScript files
│ └── includes/ # PHP files for backend functionality
│ └── RegisterColors.php
├── frontend/
│ └── assets/
│ ├── css/ # Frontend CSS files
│ │ └── index.style.css
│ └── js/ # Frontend JavaScript files
├── style.css # Main theme stylesheet
└── functions.php # Theme functions


### Features

- **Automatic Asset Loading**: All assets in the defined directories are automatically loaded
- **Version Control**: Files are versioned using their modification time
- **Environment Separation**: Clear separation between frontend and backend assets
- **PHP Includes**: Automatic loading of PHP files from backend/assets/includes
- **Error Handling**: Built-in error logging for failed asset loading

### Asset Types

The Asset Manager handles three types of assets:
1. CSS files (both frontend and backend)
2. JavaScript files (both frontend and backend)
3. PHP includes (backend only)

### Usage

Assets are automatically loaded based on their location:

- Frontend assets are loaded on the public site
- Backend assets are loaded in the WordPress admin
- PHP files in `backend/assets/includes` are automatically included

No manual registration is required - simply place your files in the appropriate directory:
