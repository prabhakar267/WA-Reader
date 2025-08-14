// WhatsApp Chat Parser - Client-side JavaScript implementation
class WAParser {
    constructor() {
        this.DEFAULT_ERROR_MESSAGE = "It wasn't a valid text file or we were not able to convert it";
        this.ATTACHMENT_MESSAGES = [
            "Media attached",
            "Datei angehängt",
        ];
        this.TIMESTAMP_SPLITTERS = ["-", "]", ": "];
        this.REMOVE_CHARACTERS = ["[", "]", "(", ")", "{", "}", '\u200e', '\ufeff', '\u202d', '\u202c'];
    }

    // Check if file type is allowed
    allowedFile(filename) {
        const allowedFiletypes = ['txt', 'json', 'zip'];
        return filename.includes('.') && 
               allowedFiletypes.includes(filename.split('.').pop().toLowerCase());
    }

    // Check if a string contains media attachment message
    isMediaString(chatString) {
        for (const mediaMessage of this.ATTACHMENT_MESSAGES) {
            if (chatString.toLowerCase().includes(mediaMessage.toLowerCase())) {
                return { isMedia: true, mediaMessage };
            }
        }
        return { isMedia: false, mediaMessage: null };
    }

    // Parse date string with multiple formats
    parseDateTime(dateString) {
        // Remove unwanted characters
        let cleanDateString = dateString;
        for (const char of this.REMOVE_CHARACTERS) {
            cleanDateString = cleanDateString.replace(new RegExp('\\' + char, 'g'), '');
        }

        // Try different date formats commonly found in WhatsApp exports
        
        // Format 1: "13/10/2017, 12:12 a.m." or "13/10/2017, 12:12 AM" (US format)
        const usDateTimeRegex = /^(\d{1,2})\/(\d{1,2})\/(\d{2,4}),?\s*(\d{1,2}):(\d{2})\s*(a\.m\.|p\.m\.|AM|PM)?/i;
        const usMatch = cleanDateString.match(usDateTimeRegex);
        
        if (usMatch) {
            const [, day, month, year, hour, minute, ampm] = usMatch;
            let hour24 = parseInt(hour);
            
            // Convert to 24-hour format if AM/PM is present
            if (ampm) {
                const isPM = ampm.toLowerCase().includes('p');
                if (isPM && hour24 !== 12) {
                    hour24 += 12;
                } else if (!isPM && hour24 === 12) {
                    hour24 = 0;
                }
            }
            
            // Create date object (month is 0-indexed in JavaScript)
            const fullYear = parseInt(year) < 100 ? 2000 + parseInt(year) : parseInt(year);
            const date = new Date(fullYear, parseInt(month) - 1, parseInt(day), hour24, parseInt(minute));
            
            if (!isNaN(date.getTime())) {
                return date;
            }
        }

        // Format 2: "28.05.21, 21:44" (European format DD.MM.YY with comma)
        const euDateTimeRegex = /^(\d{1,2})\.(\d{1,2})\.(\d{2,4}),?\s*(\d{1,2}):(\d{2})$/;
        const euMatch = cleanDateString.match(euDateTimeRegex);
        
        if (euMatch) {
            const [, day, month, year, hour, minute] = euMatch;
            
            // Handle 2-digit years (assume 20xx)
            const fullYear = parseInt(year) < 100 ? 2000 + parseInt(year) : parseInt(year);
            
            // Create date object (month is 0-indexed in JavaScript)
            const date = new Date(fullYear, parseInt(month) - 1, parseInt(day), parseInt(hour), parseInt(minute));
            
            if (!isNaN(date.getTime())) {
                return date;
            }
        }

        // Format 3: "28/05/21 21.44" (Alternative European format DD/MM/YY HH.MM)
        const altEuDateTimeRegex = /^(\d{1,2})\/(\d{1,2})\/(\d{2,4})\s+(\d{1,2})\.(\d{2})$/;
        const altEuMatch = cleanDateString.match(altEuDateTimeRegex);
        
        if (altEuMatch) {
            const [, day, month, year, hour, minute] = altEuMatch;
            
            // Handle 2-digit years (assume 20xx)
            const fullYear = parseInt(year) < 100 ? 2000 + parseInt(year) : parseInt(year);
            
            // Create date object (month is 0-indexed in JavaScript)
            const date = new Date(fullYear, parseInt(month) - 1, parseInt(day), parseInt(hour), parseInt(minute));
            
            if (!isNaN(date.getTime())) {
                return date;
            }
        }

        // Format 3: "YYYY-MM-DD HH:MM" (ISO-like format)
        const isoDateTimeRegex = /^(\d{4})-(\d{1,2})-(\d{1,2})\s*(\d{1,2}):(\d{2})$/;
        const isoMatch = cleanDateString.match(isoDateTimeRegex);
        
        if (isoMatch) {
            const [, year, month, day, hour, minute] = isoMatch;
            const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day), parseInt(hour), parseInt(minute));
            
            if (!isNaN(date.getTime())) {
                return date;
            }
        }

        // Final fallback: try native Date parsing
        try {
            const date = new Date(cleanDateString);
            if (!isNaN(date.getTime())) {
                return date;
            }
        } catch (e) {
            // Continue to next attempt
        }

        throw new Error('Invalid date format');
    }

    // Parse a single line of chat
    parseLine(inputLine, personsList, mediaFiles = {}) {
        let timestampString = null;
        let line = null;

        // Try different timestamp splitters
        for (const splitter of this.TIMESTAMP_SPLITTERS) {
            const items = inputLine.split(splitter);
            const dirtyTimestampString = items[0].trim(); // Trim whitespace

            try {
                timestampString = this.parseDateTime(dirtyTimestampString);
                line = items.slice(1).join(splitter).trim();
                break;
            } catch (e) {
                continue;
            }
        }

        if (!timestampString) {
            throw new Error('No valid timestamp found');
        }

        const items = line.split(':');
        const textString = items.slice(1).join(':').trim();
        
        if (!textString) {
            return { parsedLine: null, personsList };
        }

        const userName = items[0].trim();
        if (userName && !personsList.includes(userName)) {
            personsList.push(userName);
        }

        let mediaPath = null;
        let isMediaStringFlag = false;

        // Check for media attachments
        const mediaCheck = this.isMediaString(textString);
        if (mediaCheck.isMedia) {
            isMediaStringFlag = true;
            // Look for media file in the provided media files
            // Extract filename from text like "VID-20210528-WA0002.mp4 (Datei angehängt)"
            const fileNameMatch = textString.match(/([A-Z]{3}-\d{8}-WA\d{4}\.\w+)/);
            if (fileNameMatch) {
                const fileName = fileNameMatch[1];
                if (mediaFiles[fileName]) {
                    mediaPath = mediaFiles[fileName];
                }
            }
            
            // Fallback: try splitting by words and cleaning
            if (!mediaPath) {
                const words = textString.split(/\s+/);
                for (const word of words) {
                    const cleanWord = word.replace(/[\u200e\u202d\u202c()]/g, '').trim();
                    if (mediaFiles[cleanWord]) {
                        mediaPath = mediaFiles[cleanWord];
                        break;
                    }
                }
            }
        }

        const chatStringObject = {
            t: timestampString.toISOString(),
            p: textString, // Always keep original text for filename extraction
            i: personsList.indexOf(userName),
            m: isMediaStringFlag,
            mp: mediaPath
        };

        return { parsedLine: chatStringObject, personsList };
    }

    // Parse text content (from .txt file)
    async parseTextContent(content, mediaFiles = {}) {
        const lines = content.split('\n');
        const parsedChats = [];
        let personsList = [];

        for (const line of lines) {
            const trimmedLine = line.trim();
            if (!trimmedLine) continue;

            try {
                const { parsedLine, personsList: updatedPersonsList } = 
                    this.parseLine(trimmedLine, personsList, mediaFiles);
                
                personsList = updatedPersonsList;
                
                if (parsedLine) {
                    parsedChats.push(parsedLine);
                } else if (parsedChats.length > 0) {
                    // Continuation of previous message
                    parsedChats[parsedChats.length - 1].p += '\n' + trimmedLine;
                }
            } catch (e) {
                if (parsedChats.length === 0) {
                    throw new Error(this.DEFAULT_ERROR_MESSAGE);
                } else {
                    // Continuation message from last message
                    parsedChats[parsedChats.length - 1].p += '\n' + trimmedLine;
                }
            }
        }

        return { chat: parsedChats, users: personsList };
    }

    // Parse JSON content
    async parseJsonContent(content) {
        try {
            const chatArchive = JSON.parse(content);
            if (chatArchive.users && chatArchive.chat) {
                return { chat: chatArchive.chat, users: chatArchive.users };
            } else {
                throw new Error(this.DEFAULT_ERROR_MESSAGE);
            }
        } catch (e) {
            throw new Error(this.DEFAULT_ERROR_MESSAGE);
        }
    }

    // Parse ZIP file content
    async parseZipContent(file) {
        try {
            const zip = await JSZip.loadAsync(file);
            const mediaFiles = {};
            let chatContent = null;

            // Extract all files from ZIP
            for (const [filename, zipEntry] of Object.entries(zip.files)) {
                if (zipEntry.dir) continue;

                if (filename.endsWith('_chat.txt') || filename.endsWith('.txt')) {
                    // This is the chat file
                    chatContent = await zipEntry.async('text');
                } else {
                    // This is a media file
                    const blob = await zipEntry.async('blob');
                    const url = URL.createObjectURL(blob);
                    mediaFiles[filename] = url;
                }
            }

            if (!chatContent) {
                throw new Error('No chat file found in ZIP');
            }

            const result = await this.parseTextContent(chatContent, mediaFiles);
            return {
                ...result,
                attachments: true,
                mediaFiles
            };
        } catch (e) {
            throw new Error('Failed to process ZIP file: ' + e.message);
        }
    }

    // Main parsing function
    async parseFile(file) {
        if (!this.allowedFile(file.name)) {
            throw new Error('Please upload a valid file!');
        }

        const fileExtension = file.name.split('.').pop().toLowerCase();

        try {
            if (fileExtension === 'zip') {
                return await this.parseZipContent(file);
            } else if (fileExtension === 'json') {
                const content = await this.readFileAsText(file);
                const result = await this.parseJsonContent(content);
                return { ...result, attachments: false };
            } else if (fileExtension === 'txt') {
                const content = await this.readFileAsText(file);
                const result = await this.parseTextContent(content);
                return { ...result, attachments: false };
            } else {
                throw new Error('Unsupported file type');
            }
        } catch (e) {
            throw new Error(e.message || this.DEFAULT_ERROR_MESSAGE);
        }
    }

    // Helper function to read file as text
    readFileAsText(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => resolve(e.target.result);
            reader.onerror = (e) => reject(new Error('Failed to read file'));
            reader.readAsText(file, 'utf-8');
        });
    }

    // Clean up media URLs when done
    cleanupMediaFiles(mediaFiles) {
        if (mediaFiles) {
            Object.values(mediaFiles).forEach(url => {
                if (url.startsWith('blob:')) {
                    URL.revokeObjectURL(url);
                }
            });
        }
    }
}

// Make WAParser available globally
window.WAParser = WAParser;
