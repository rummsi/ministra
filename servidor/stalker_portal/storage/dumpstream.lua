
-- Copyright (C) 2014-2015, Andrey Dyldin <and@cesbo.com>

-- Permission is hereby granted, free of charge, to any person obtaining a copy
-- of this software and associated documentation files (the "Software"), to deal
-- in the Software without restriction, including without limitation the rights
-- to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
-- copies of the Software, and to permit persons to whom the Software is
-- furnished to do so, subject to the following conditions:

-- The above copyright notice and this permission notice shall be included in
-- all copies or substantial portions of the Software.

-- THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
-- IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
-- FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
-- AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
-- LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
-- OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
-- THE SOFTWARE.

input_conf = { format = "udp", addr = "224.0.1.2", port = 1234 }
output_path = "."
pieces_number = 5
callback_url = nil
instance = nil
current_hour = 25
output_file = nil

function send_callback(content)
    local http_conf = {
        host = callback_url.host,
        port = callback_url.port,
        path = callback_url.path,
        method = "PUT",
        content = content,
        headers = {
            "User-Agent: " .. http_user_agent,
            "Host: " .. callback_url.host,
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " .. #content,
            "Connection: close",
        },
    }

    if callback_url.login and callback_url.password then
        local auth = base64.encode(callback_url.login .. ":" .. callback_url.password)
        table.insert(http_conf.headers, "Authorization: Basic " .. auth)
    end

    http_conf.callback = function(self, response)
        if not response then
            return nil
        end

        if response.code ~= 200 then
            log.error("[dumpstream] failed to send start/stop params")
        end
    end

    http_request(http_conf)
end

function stop_channel()
    if instance then
        if output_file then
            if callback_url ~= nil then
                send_callback("action=ended")
            end
        else
            if callback_url ~= nil then
                send_callback("end_time=" .. tostring(os.time()))
            end
        end

        kill_input(instance.input)
        instance.input = nil
        instance.output = nil
        instance = nil
        collectgarbage()
    end
end

function start_channel()
    stop_channel()
    instance = {}

    local filename = nil
    if output_file then
        if callback_url ~= nil then
            send_callback("action=started")
        end
        filename = output_file
    else
        if callback_url ~= nil then
            send_callback("start_time=" .. tostring(os.time()))
        end
        filename = output_path .. "/" .. os.date("%Y%m%d-%H") .. ".mpg"
    end

    input_conf.name = "dumpstream"
    input_conf.no_analyze = true
    instance.input = init_input(input_conf)
    instance.output = file_output({
        upstream = instance.input.tail:stream(),
        filename = filename,
    })

    local l = {}
    for d in utils.readdir(output_path) do table.insert(l, d) end
    if #l > pieces_number then
        table.sort(l)
        os.remove(output_path .. "/" .. l[1])
    end
end

length = 0
start_delay = 0

options_usage = [[
    -A    URL         source address (format://address)
    -a    ADDR        source UDP address (option depricated)
    -p    PORT        source UDP port (default: 1234) (option depricated)
    -d    PATH        directory to save pieces
    -n    X           number of pieces
    -c    URL         callback url, use to send HTTP PUT
                      with start_time/end_time params
    -l    LENGTH      recording length
    -s    DELAY       delay before start recording
    -o    NAME        save output to file with specified name
]]

options = {
    ["-A"] = function(idx)
        input_conf = parse_url(argv[idx + 1])
        return 1
    end,
    ["-a"] = function(idx)
        input_conf.addr = argv[idx + 1]
        return 1
    end,
    ["-p"] = function(idx)
        input_conf.port = tonumber(argv[idx + 1])
        return 1
    end,
    ["-d"] = function(idx)
        output_path = argv[idx + 1]
        if output_path:sub(-1) == "/" then output_path = output_path:sub(1, -2) end
        return 1
    end,
    ["-n"] = function(idx)
        pieces_number = tonumber(argv[idx + 1])
        return 1
    end,
    ["-c"] = function(idx)
        callback_url = parse_url(argv[idx + 1])
        return 1
    end,
    ["-l"] = function(idx)
        length = tonumber(argv[idx + 1])
        return 1
    end,
    ["-s"] = function(idx)
        start_delay = tonumber(argv[idx + 1])
        return 1
    end,
    ["-o"] = function(idx)
        output_file = argv[idx + 1]
        return 1
    end,
}

function on_sighup()
    stop_channel()
    timer({
        interval = 1,
        callback = function(self)
            self:close()
            astra.exit()
        end,
    })
end

function main()
    if start_delay > 0 then
        timer({
            interval = start_delay,
            callback = function(self)
                self:close()
                start_delay = 0
                main()
            end,
        })
        return
    end

    current_hour = os.date("*t").hour
    start_channel()

    if length > 0 then
        timer({
            interval = length,
            callback = function(self)
                self:close()
                stop_channel()
                astra.exit()
            end,
        })
    else
        timer({
            interval = 60,
            callback = function(self)
                local ct = os.date("*t")
                if ct.hour ~= current_hour then
                    current_hour = ct.hour
                    start_channel()
                end
            end,
        })
    end
end
