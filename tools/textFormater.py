import sys
import os

if __name__ == "__main__":
    if len(sys.argv) == 2:
        file_path = sys.argv[1]
        if os.path.exists(file_path):
            content = ''
            with open(file_path, 'r') as file:
                for line in file:
                    content += line.replace("\n", "\\n")
            print(content)
            with open('output_content.txt', 'w', encoding='UTF-8') as file:
                file.write(content)
        else:
            print(f"The file '{file_path}' does not exist.")
    else:
        print("No arguments provided.")