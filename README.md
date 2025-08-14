# WA Reader - Client-Side WhatsApp Chat Viewer

WA Reader is a web-tool aimed to help users view their WhatsApp back-up chats in a familiar user-interface. This version runs entirely in your browser with no backend required - perfect for GitHub Pages deployment.

## 🌟 Features

- **100% Client-Side**: All processing happens in your browser - no data is sent to any server
- **Privacy First**: Your chat files never leave your device
- **Multiple Formats**: Supports `.txt`, `.json`, and `.zip` files
- **Media Support**: View images, videos, and audio files from ZIP exports
- **Easy Deployment**: Can be hosted on GitHub Pages or any static hosting service
- **No Installation**: Just open in your browser and use

## 🚀 Live Demo

Visit the live demo at: [Your GitHub Pages URL]

## 📱 How to Export WhatsApp Chats

### iPhone
1. Open WhatsApp and go to the chat you want to export
2. Tap the contact/group name at the top
3. Scroll down and tap "Export Chat"
4. Choose "Without Media" for `.txt` or "With Media" for `.zip`

### Android
1. Open WhatsApp and go to the chat you want to export
2. Tap the three dots menu (⋮) in the top right
3. Tap "More" → "Export chat"
4. Choose "Without Media" for `.txt` or "With Media" for `.zip`

## 🛠️ Local Development

To run this locally:

1. Clone the repository:
```bash
git clone https://github.com/prabhakar267/WA-Reader.git
cd WA-Reader
```

2. Serve the files using any static server:
```bash
# Using Python 3
python -m http.server 8000

# Using PHP
php -S localhost:8000

# Using Live Server extension in VS Code
# Right-click on index.html and select "Open with Live Server"
```

3. Open your browser and navigate to `http://localhost:8000`

## 📁 Project Structure

```
WA-Reader/
├── index.html              # Main HTML file
├── static/
│   ├── css/
│   │   ├── style.css       # Main styles
│   │   └── minEmoji2.css   # Emoji styles
│   ├── js/
│   │   ├── wa-parser.js    # WhatsApp chat parser
│   │   ├── script.js       # Main application logic
│   │   └── jMinEmoji2.min.js # Emoji rendering
│   └── img/                # Images and icons
├── sample/                 # Sample chat files for testing
└── README.md
```

## 🔧 Technical Details

### Client-Side Architecture

This version has been completely rewritten to work without a backend:

- **File Processing**: Uses the File API to read uploaded files
- **ZIP Handling**: Uses JSZip library to extract ZIP files and media
- **Date Parsing**: Robust date parsing for different WhatsApp export formats
- **Media Display**: Creates blob URLs for media files from ZIP archives
- **Memory Management**: Properly cleans up blob URLs to prevent memory leaks

### Supported File Formats

- **`.txt`**: Plain text chat export (without media)
- **`.json`**: Previously processed chat data
- **`.zip`**: Chat export with media files (images, videos, audio)

### Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 11+
- Edge 79+

## 🚀 GitHub Pages Deployment

1. Fork this repository
2. Go to your repository settings
3. Scroll to "Pages" section
4. Select "Deploy from a branch"
5. Choose "main" branch and "/ (root)" folder
6. Your site will be available at `https://yourusername.github.io/WA-Reader`

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly with different chat formats
5. Submit a pull request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Original Python version by [Prabhakar Gupta](https://github.com/prabhakar267)
- All contributors who helped shape the project
- WhatsApp for providing chat export functionality

## 🐛 Issues & Support

If you encounter any issues:

1. Check if your chat file format is supported
2. Try with a smaller chat file first
3. Open an issue on GitHub with:
   - Your WhatsApp version
   - Device type (iPhone/Android)
   - Error message (if any)
   - Sample of your chat format (remove personal info)

## 🔒 Privacy & Security

- **No Server Communication**: All processing happens locally in your browser
- **No Data Storage**: Files are processed in memory and discarded
- **No Analytics**: No tracking or analytics code included
- **Open Source**: Full source code available for audit

Your privacy is our priority. This tool never sends your chat data anywhere - it all stays on your device.
